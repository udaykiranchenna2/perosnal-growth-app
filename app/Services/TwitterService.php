<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Noweh\TwitterApi\Client;

class TwitterService
{
    protected $twitterConfig;
    protected $twitteroauth;

    public function __construct()
    {
        $this->twitterConfig = [
            'account_id' => config("services.twitter.account_id"),
            'access_token' => config("services.twitter.access_token"),
            'access_token_secret' => config("services.twitter.access_token_secret"),
            'consumer_key' => config("services.twitter.api_key"),
            'consumer_secret' => config("services.twitter.api_secret"),
            'bearer_token' => config("services.twitter.bearer_token"),
            'free_mode' => false
        ];

        $this->twitteroauth = new Client($this->twitterConfig);

    }

    protected function validateConfig()
    {
        $requiredFields = [
            'account_id',
            'access_token',
            'access_token_secret',
            'consumer_key',
            'consumer_secret',
            'bearer_token'
        ];

        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (empty($this->twitterConfig[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            throw new InvalidArgumentException(
                "Missing required Twitter API credentials: " . implode(', ', $missingFields) .
                ". Please check your .env file."
            );
        }
    }

    /**
     * Post a tweet to Twitter
     *
     * @param string $content The tweet content
     * @return array|false Returns array with tweet data on success, false on failure
     */
    public function postTweet(string $content)
    {
        try {
            $response = $this->twitteroauth->tweet()->create()
                ->performRequest(
                    ['text' => $content],
                    withHeaders: true
                );

            if (isset($response->data->id)) {
                return [
                    'success' => true,
                    'tweet_id' => $response->data->id,
                    'response' => $response
                ];
            }

            Log::error('Twitter API Error - Invalid Response', [
                'response' => $response,
                'content' => $content
            ]);

            return false;
        } catch (Exception $e) {
            $errorData = [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'content' => $content
            ];

            // Check for authentication errors
            if ($e->getCode() === 401) {
                Log::error('Twitter API Authentication Error', $errorData);
                throw new InvalidArgumentException("Twitter API authentication failed. Please check your credentials.");
            }

            Log::error('Twitter API Exception', $errorData);
            return false;
        }
    }

    /**
     * Get tweet details
     *
     * @param string $tweetId The tweet ID
     * @return array|false Returns array with tweet data on success, false on failure
     */
    public function getTweet(string $tweetId)
    {
        try {
            $response = $this->twitteroauth->tweet()->get($tweetId)
                ->performRequest(withHeaders: true);

            if (isset($response->data)) {
                return [
                    'success' => true,
                    'tweet' => $response->data
                ];
            }

            return false;
        } catch (Exception $e) {
            $errorData = [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'tweet_id' => $tweetId
            ];

            if ($e->getCode() === 401) {
                Log::error('Twitter API Authentication Error', $errorData);
                throw new InvalidArgumentException("Twitter API authentication failed. Please check your credentials.");
            }

            Log::error('Twitter API Error - Get Tweet', $errorData);
            return false;
        }
    }

    /**
     * Delete a tweet
     *
     * @param string $tweetId The tweet ID
     * @return bool Returns true on success, false on failure
     */
    public function deleteTweet(string $tweetId)
    {
        try {
            $response = $this->twitteroauth->tweet()->delete($tweetId)
                ->performRequest(withHeaders: true);

            return isset($response->data->deleted) && $response->data->deleted === true;
        } catch (Exception $e) {
            $errorData = [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'tweet_id' => $tweetId
            ];

            if ($e->getCode() === 401) {
                Log::error('Twitter API Authentication Error', $errorData);
                throw new InvalidArgumentException("Twitter API authentication failed. Please check your credentials.");
            }

            Log::error('Twitter API Error - Delete Tweet', $errorData);
            return false;
        }
    }
}
