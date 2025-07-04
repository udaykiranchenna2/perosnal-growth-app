<?php

namespace App\Jobs;

use App\Models\XPostSettings;
use App\Models\GeneratedTweet;
use App\Models\TweetContext;
use App\Models\XCommunity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Exceptions\ErrorException;

class GenerateTweetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $settings;
    protected $context;
    protected $instructions;
    protected $community;
    protected $options;

    public function __construct(XPostSettings $settings, TweetContext $context, string $instructions, ?XCommunity $community = null, array $options = [])
    {
        $this->settings = $settings;
        $this->context = $context;
        $this->instructions = $instructions;
        $this->community = $community;
        $this->options = $options;
    }

    public function handle()
    {
        try {
            // Set job as queued
            $this->settings->update(['x_post_job_queued' => true]);
            // Build your prompt as a simple string
            $promptText = "You are {$this->settings->profile_name}. {$this->settings->about_me}\n\n";
            $promptText .= "Personality Traits: {$this->settings->personality}\n\n";
            
            $promptText .= "TASK: Generate a unique, engaging tweet based on the user's request and context below.\n\n";
            
            $promptText .= "Context for tweet style/topic:\n{$this->context->context}\n\n";
            
            if ($this->community) {
                $promptText .= "Target Community: '{$this->community->name}'\n";
                $promptText .= "Community Focus: {$this->community->description}\n";
                $promptText .= "Make the tweet relevant to this community's interests.\n\n";
            }
            
            $promptText .= "User's Request/Topic: {$this->instructions}\n\n";
            
            $promptText .= "INSTRUCTIONS:\n";
            $promptText .= "- Create a NEW tweet inspired by the user's request, don't copy it exactly\n";
            $promptText .= "- Make it engaging and reflect your personality\n";
            $promptText .= "- Keep within {$this->settings->max_tweet_length} characters\n";
            
            // Handle tone option
            $tone = $this->options['tone'] ?? 'professional';
            $promptText .= "- Use a {$tone} tone\n";
            
            // Handle hashtags option
            if ($this->options['include_hashtags'] ?? true) {
                $promptText .= "- Include 2-4 relevant hashtags\n";
            } else {
                $promptText .= "- DO NOT include any hashtags\n";
            }
            
            // Handle emojis option
            $maxEmojis = $this->options['max_emojis'] ?? 2;
            if ($maxEmojis == 0) {
                $promptText .= "- DO NOT use any emojis\n";
            } elseif ($maxEmojis == 1) {
                $promptText .= "- Use maximum 1 emoji, place it strategically\n";
            } elseif ($maxEmojis <= 3) {
                $promptText .= "- Use maximum {$maxEmojis} emojis, place them naturally\n";
            } else {
                $promptText .= "- Use emojis freely but don't overdo it (max {$maxEmojis})\n";
            }
            
            // Handle max lines option
            $maxLines = $this->options['max_lines'] ?? 2;
            if ($maxLines == 1) {
                $promptText .= "- Keep it to SINGLE line only, no line breaks\n";
            } elseif ($maxLines == 2) {
                $promptText .= "- Maximum 2 lines, use line breaks strategically\n";
            } else {
                $promptText .= "- Maximum {$maxLines} lines, keep it concise\n";
            }
            
            // Handle writing style
            $writingStyle = $this->options['writing_style'] ?? 'normal';
            switch ($writingStyle) {
                case 'thread':
                    $promptText .= "- Write in thread-style format (use numbers or bullets)\n";
                    break;
                case 'question':
                    $promptText .= "- Format as a question to encourage engagement\n";
                    break;
                case 'list':
                    $promptText .= "- Use list format with bullet points or numbers\n";
                    break;
                case 'story':
                    $promptText .= "- Write as a brief story or personal anecdote\n";
                    break;
                default:
                    $promptText .= "- Use normal tweet format\n";
                    break;
            }
            
            // Handle call to action
            $ctaType = $this->options['cta_type'] ?? 'none';
            switch ($ctaType) {
                case 'engage':
                    $promptText .= "- End with a call to action for likes/comments (e.g., 'What do you think?')\n";
                    break;
                case 'share':
                    $promptText .= "- Ask readers to share their experiences or opinions\n";
                    break;
                case 'follow':
                    $promptText .= "- Include a subtle 'follow for more' type call to action\n";
                    break;
                case 'question':
                    $promptText .= "- End with a specific question to spark discussion\n";
                    break;
                default:
                    $promptText .= "- No specific call to action needed\n";
                    break;
            }
            
            $promptText .= "- Make it unique and valuable to readers\n";
            $promptText .= "- Generate a DIFFERENT variation each time to ensure uniqueness\n\n";
            
            $promptText .= "FORMAT: Respond ONLY with valid JSON:\n";
            $promptText .= "{\"tweet\": \"Your generated tweet here\"}";

            info('promptText: ' . $promptText);

            $response = Gemini::geminiFlash()->generateContent($promptText);

            $content = $response->text();
            info('Raw Content: ' . $content);

            $tweetContent = $this->extractTweetFromResponse($content);

            GeneratedTweet::create([
                'x_post_settings_id' => $this->settings->id,
                'x_community_id' => $this->community?->id,
                'content' => $tweetContent,
                'is_sent' => false
            ]);

            // Set job as completed
            $this->settings->update(['x_post_job_queued' => false]);
        } catch (\Exception $e) {
            // Set job as completed even if there's an error
            $this->settings->update(['x_post_job_queued' => false]);
            \Log::error('Failed to generate tweet: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Extract tweet content from response regardless of format
     */
    private function extractTweetFromResponse($content)
    {
        // Remove markdown code blocks if present
        $content = preg_replace('/```(?:json)?\s*/', '', $content);
        $content = preg_replace('/```\s*$/', '', $content);
        $content = trim($content);

        // Try direct JSON decode
        $jsonData = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE && isset($jsonData['tweet'])) {
            return $jsonData['tweet'];
        }

        // Try to extract JSON pattern (handles cases where there's text around the JSON)
        if (preg_match('/\{.*?"tweet"\s*:\s*"(.*?)"\s*\}/s', $content, $matches)) {
            return $matches[1];
        }

        // Final fallback: just return the content as is
        return $content;
    }
}
