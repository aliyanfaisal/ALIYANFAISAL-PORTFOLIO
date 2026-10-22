<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class EmailLog extends Model
{
    use Prunable;

    /**
     * A send never confirmed after this many minutes is shown as failed.
     */
    public const STALE_AFTER_MINUTES = 10;

    protected $fillable = ['to', 'subject', 'status', 'error'];

    /**
     * Only metadata is kept, never the body: OTP emails contain live codes.
     */
    public function displayStatus(): string
    {
        if ($this->status === 'sending' && $this->created_at->lt(now()->subMinutes(self::STALE_AFTER_MINUTES))) {
            return 'failed';
        }

        return $this->status;
    }

    public function prunable(): Builder
    {
        return static::where('created_at', '<', now()->subDays(90));
    }
}
