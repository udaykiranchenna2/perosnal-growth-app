<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class XCommunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'x_post_settings_id',
        'name',
        'community_id',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function settings(): BelongsTo
    {
        return $this->belongsTo(XPostSettings::class, 'x_post_settings_id');
    }
}
