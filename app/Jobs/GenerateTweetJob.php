<?php

namespace App\Jobs;

use App\Models\XPostSettings;
use App\Models\GeneratedTweet;
use App\Models\TweetContext;
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

    public function __construct(XPostSettings $settings, TweetContext $context, string $instructions)

    {
        $this->settings = $settings;
        $this->context = $context;
        $this->instructions = $instructions;
    }

    public function handle()
    {
        try {
            // Set job as queued
            $this->settings->update(['x_post_job_queued' => true]);
            // Build your prompt as a simple string
            $promptText = "You are {$this->settings->profile_name}. {$this->settings->about_me}\n\n";
            $promptText .= "Personality Traits: {$this->settings->personality}\n\n";
            $promptText .= "Instructions: Based on the context provided below, generate a professional and engaging tweet that reflects your personality. Keep the tweet within {$this->settings->max_tweet_length} characters.\n\n";
            $promptText .= "Context:\n{$this->context->context}\n\n";
            $promptText .= "YOU MUST FORMAT YOUR RESPONSE AS JSON. Respond ONLY with valid JSON in this format:\n";
            $promptText .= "{\"tweet\": \"Your tweet text here\"}\n\n";
            $promptText .= "DO NOT include any explanations, formatting, or text outside of the JSON structure.";

            $promptText .= "\n\***Main Prompt : {$this->instructions}";

            info('promptText: ' . $promptText);

            $response = Gemini::geminiFlash()->generateContent($promptText);

            $content = $response->text();
            info('Raw Content: ' . $content);

            $tweetContent = $this->extractTweetFromResponse($content);

            GeneratedTweet::create([
                'x_post_settings_id' => $this->settings->id,
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
