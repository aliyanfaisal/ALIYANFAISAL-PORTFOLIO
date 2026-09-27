<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostReaction extends Model
{
    /**
     * The emoji reactions visitors may choose from.
     *
     * @var array<int, string>
     */
    public const EMOJIS = ['👍', '❤️', '😂', '🎉'];

    protected $fillable = [
        'blog_post_id', 'session_id', 'emoji',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class, 'blog_post_id');
    }
}
