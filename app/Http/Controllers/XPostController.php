<?php

namespace App\Http\Controllers;

use App\Models\XPostSettings;
use App\Models\GeneratedTweet;
use App\Models\TweetContext;
use App\Jobs\GenerateTweetJob;
use App\Services\TwitterService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class XPostController extends Controller
{
    protected $twitterService;

    public function __construct(TwitterService $twitterService)
    {
        $this->twitterService = $twitterService;
    }

    public function index()
    {
        $settings = XPostSettings::getProfile();
        return Inertia::render('XPost/Index', [
            'settings' => $settings->load('contexts')
        ]);
    }

    public function update(Request $request)
    {
        $settings = XPostSettings::getProfile();

        $validated = $request->validate([
            'profile_name' => 'required|string|max:255',
            'about_me' => 'required|string',
            'personality' => 'required|string',
            'max_tweet_length' => 'required|integer|min:1|max:280'
        ]);

        $settings->update($validated);

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function storeContext(Request $request)
    {
        $settings = XPostSettings::getProfile();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'context' => 'required|string',
            'is_default' => 'boolean'
        ]);

        $context = $settings->contexts()->create($validated);

        return redirect()->back()->with('success', 'Context added successfully.');
    }

    public function updateContext(Request $request, TweetContext $context)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'context' => 'required|string',
            'is_default' => 'boolean'
        ]);

        $context->update($validated);

        return redirect()->back()->with('success', 'Context updated successfully.');
    }

    public function destroyContext(TweetContext $context)
    {
        $context->delete();
        return redirect()->back()->with('success', 'Context deleted successfully.');
    }

    public function generateTweet(Request $request)
    {
        $settings = XPostSettings::getProfile();
        
        $validated = $request->validate([
            'context_id' => 'required|exists:tweet_contexts,id'
        ]);

        $context = TweetContext::find($validated['context_id']);
        GenerateTweetJob::dispatch($settings, $context);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Tweet generation job has been queued.']);
        }

        return redirect()->back()->with('success', 'Tweet generation job has been queued.');
    }

    public function listTweets()
    {
        $settings = XPostSettings::getProfile();
        $tweets = $settings->tweets()->latest()->get();
        return Inertia::render('XPost/Tweets', [
            'tweets' => $tweets,
            'settings' => $settings
        ]);
    }

    public function markAsSent(GeneratedTweet $tweet, TwitterService $twitterService)
    {
        try {
            // Post the tweet to Twitter
            $result = $twitterService->postTweet($tweet->content);

            if ($result['success']) {
                // Update the tweet status
                $tweet->update([
                    'is_sent' => true,
                    'sent_at' => now(),
                    'tweet_id' => $result['tweet_id']
                ]);

                return back()->with('success', 'Tweet posted successfully!');
            }

            return back()->with('error', 'Failed to post tweet: ' . ($result['error'] ?? 'Unknown error'));
        } catch (\Exception $e) {
            Log::error('Failed to post tweet', [
                'error' => $e->getMessage(),
                'tweet_id' => $tweet->id
            ]);

            return back()->with('error', 'Failed to post tweet: ' . $e->getMessage());
        }
    }

    public function destroy(GeneratedTweet $tweet)
    {
        try {
            // Only attempt to delete from Twitter if the tweet has been sent
            if ($tweet->is_sent && $tweet->tweet_id) {
                $this->twitterService->deleteTweet($tweet->tweet_id);
            }
            $tweet->delete();
            return redirect()->back()->with('success', 'Tweet deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete tweet', [
                'error' => $e->getMessage(),
                'tweet_id' => $tweet->id
            ]);
            return redirect()->back()->with('error', 'Failed to delete tweet. Please try again later.');
        }
    }
}
