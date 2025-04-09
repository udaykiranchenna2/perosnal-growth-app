<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SharedContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'sticky_note_id',
        'slug'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($content) {
            if (empty($content->slug)) {
                $content->slug = Str::random(10);
            }
        });
    }

    public function stickyNote()
    {
        return $this->belongsTo(StickyNote::class);
    }
} 