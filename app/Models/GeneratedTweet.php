<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratedTweet extends Model
{
    use HasFactory;

    protected $fillable = [
        'x_post_settings_id',
        'content',
        'is_sent',
        'sent_at',
        'tweet_id'
    ];

    protected $casts = [
        'is_sent' => 'boolean',
        'sent_at' => 'datetime'
    ];

    public function settings(): BelongsTo
    {
        return $this->belongsTo(XPostSettings::class, 'x_post_settings_id');
    }
} 