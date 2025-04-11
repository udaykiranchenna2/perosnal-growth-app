<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class XPostSettings extends Model
{
    use HasFactory;

    protected $table = 'x_post_settings';

    protected $fillable = [
        'profile_name',
        'about_me',
        'personality',
        'max_tweet_length'
    ];

    protected $casts = [
        'max_tweet_length' => 'integer'
    ];

    public function tweets(): HasMany
    {
        return $this->hasMany(GeneratedTweet::class, 'x_post_settings_id');
    }

    public function contexts(): HasMany
    {
        return $this->hasMany(TweetContext::class, 'x_post_settings_id');
    }

    public function defaultContext()
    {
        return $this->contexts()->where('is_default', true)->first();
    }

    public static function getProfile()
    {
        return static::first() ?? static::create([
            'profile_name' => 'Default Profile',
            'about_me' => 'Tell us about yourself',
            'personality' => 'Describe your personality',
            'max_tweet_length' => 280
        ]);
    }
} 