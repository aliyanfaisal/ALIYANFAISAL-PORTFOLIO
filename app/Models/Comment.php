<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'blog_post_id', 'parent_id', 'name', 'email', 'body', 'status', 'session_id',
    ];

    /**
     * Foreign keys aren't cast automatically, and MySQL returns them as strings — which would
     * break strict comparisons against the (int-cast) primary key of the related post.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'blog_post_id' => 'integer',
            'parent_id' => 'integer',
        ];
    }

    /**
     * Status for a newly submitted comment, following the "auto approve comments" setting.
     */
    public static function initialStatus(): string
    {
        return Setting::current()->auto_approve_comments ? self::STATUS_APPROVED : self::STATUS_PENDING;
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function blogPost(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->where('status', self::STATUS_APPROVED)->oldest();
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(CommentReaction::class);
    }

    public function isOwnedBySession(?string $sessionId): bool
    {
        return $sessionId !== null && $this->session_id === $sessionId;
    }

    public function wasEdited(): bool
    {
        return $this->updated_at->gt($this->created_at);
    }
}
