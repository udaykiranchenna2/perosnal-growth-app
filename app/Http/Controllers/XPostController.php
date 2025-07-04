<?php

namespace App\Http\Controllers;

use App\Models\XPostSettings;
use App\Models\GeneratedTweet;
use App\Models\TweetContext;
use App\Models\XCommunity;
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
            'settings' => $settings->load(['contexts', 'communities'])
        ]);
    }

    public function update(Request $request)
    {
        $settings = XPostSettings::getProfile();

        $validated = $request->validate([
            'profile_name' => 'required|string|max:255',
            'about_me' => 'required|string',
            'personality' => 'required|string',
            'max_tweet_length' => 'required|integer|min:1|max:1200'
        ]);

        $settings->update($validated);

        return to_route('x-post.index', status: 303)->with('success', 'Settings updated successfully.');
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

        return to_route('x-post.index', status: 303)->with('success', 'Context updated successfully.');
    }

    public function destroyContext(TweetContext $context)
    {
        $context->delete();

        return to_route('x-post.index', status: 303)->with('success', 'Context deleted successfully.');
    }

    public function generateTweet(Request $request)
    {
        $settings = XPostSettings::getProfile();

        $validated = $request->validate([
            'context_id' => 'required|exists:tweet_contexts,id',
            'instructions' => 'required|string',
            'community_id' => 'nullable|exists:x_communities,id',
        ]);

        $context = TweetContext::find($validated['context_id']);
        $community = isset($validated['community_id']) ? XCommunity::find($validated['community_id']) : null;
        
        GenerateTweetJob::dispatch($settings, $context, $validated['instructions'], $community);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Tweet generation job has been queued.']);
        }

        return to_route('x-post.index', status: 303)->with('success', 'Tweet generation job has been queued.');
    }

    public function listTweets(Request $request)
    {
        $settings = XPostSettings::getProfile();
        $tweets = $settings->tweets()->with('community')->latest()->paginate($request->input('per_page', 10));
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

                return to_route('x-post.tweets', status: 303)->with('flash',
                ['status' => true, 'message' => 'Tweet sent successfully']
            );
            }


            return to_route('x-post.tweets', status: 303)->with('error', 'Failed to send tweet.');
        } catch (\Exception $e) {
            Log::error('Failed to post tweet', [
                'error' => $e->getMessage(),
                'tweet_id' => $tweet->id
            ]);

            return to_route('x-post.tweets', status: 303)->with( 'flash' , [
                'status' => false,
                'message' => $e->getMessage()
            ]);
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
            return to_route('x-post.tweets', status: 303)->with('flash',
                ['status' => true, 'message' => 'Tweet deleted successfully']
            );
        } catch (\Exception $e) {
            \Log::error('Failed to delete tweet', [
                'error' => $e->getMessage(),
                'tweet_id' => $tweet->id
            ]);

            return to_route('x-post.tweets', status: 303)->with( 'flash' , [
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function checkGenerationStatus()
    {
        $settings = XPostSettings::getProfile();
        return response()->json([
            'is_queued' => $settings->x_post_job_queued
        ]);
    }

    public function storeCommunity(Request $request)
    {
        $settings = XPostSettings::getProfile();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'community_id' => 'required|string|unique:x_communities,community_id',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $community = $settings->communities()->create($validated);

        return redirect()->back()->with('success', 'Community added successfully.');
    }

    public function updateCommunity(Request $request, XCommunity $community)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'community_id' => 'required|string|unique:x_communities,community_id,' . $community->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $community->update($validated);

        return to_route('x-post.index', status: 303)->with('success', 'Community updated successfully.');
    }

    public function destroyCommunity(XCommunity $community)
    {
        $community->delete();

        return to_route('x-post.index', status: 303)->with('success', 'Community deleted successfully.');
    }
}
