<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostView extends Model
{
    protected $fillable = [
        'post_id',
        'session_id',
        'visitor_key',
        'ip_address',
        'user_agent',
        'viewed_on',
        'viewed_at',
    ];

    protected function casts(): array
    {
        return [
            'viewed_on' => 'date',
            'viewed_at' => 'datetime',
        ];
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
