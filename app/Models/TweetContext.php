<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TweetContext extends Model
{
    use HasFactory;

    protected $fillable = [
        'x_post_settings_id',
        'name',
        'context',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean'
    ];

    public function settings()
    {
        return $this->belongsTo(XPostSettings::class, 'x_post_settings_id');
    }

    protected static function booted()
    {
        static::creating(function ($context) {
            if ($context->is_default) {
                // Remove default flag from other contexts
                static::where('x_post_settings_id', $context->x_post_settings_id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }
        });

        static::updating(function ($context) {
            if ($context->is_default) {
                // Remove default flag from other contexts
                static::where('x_post_settings_id', $context->x_post_settings_id)
                    ->where('id', '!=', $context->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }
        });
    }
} 