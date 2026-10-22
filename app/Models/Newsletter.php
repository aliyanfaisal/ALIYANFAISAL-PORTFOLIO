<?php

namespace App\Models;

use App\Enums\NewsletterFrequency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Newsletter extends Model
{
    protected $fillable = [
        'frequency', 'run_date', 'subject', 'window_start', 'window_end', 'recipients_count', 'status', 'sent_at',
    ];

    protected $casts = [
        'frequency' => NewsletterFrequency::class,
        'run_date' => 'date',
        'window_start' => 'datetime',
        'window_end' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(BlogPost::class)->withPivot('position')->orderByPivot('position');
    }
}
