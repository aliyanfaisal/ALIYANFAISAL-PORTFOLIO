<?php

namespace App\Enums;

use Carbon\CarbonInterface;

enum NewsletterFrequency: string
{
    case Daily = 'daily';
    case TwiceWeekly = 'twice_weekly';
    case Weekly = 'weekly';

    public function label(): string
    {
        return match ($this) {
            self::Daily => 'Daily',
            self::TwiceWeekly => 'Twice a week',
            self::Weekly => 'Once a week',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Daily => 'Up to 2 top posts, every day there is something new.',
            self::TwiceWeekly => 'The top 3 posts, every Monday and Thursday.',
            self::Weekly => 'The top 5 posts of the week, every Monday.',
        };
    }

    /**
     * Most posts a single email carries.
     */
    public function postLimit(): int
    {
        return match ($this) {
            self::Daily => 2,
            self::TwiceWeekly => 3,
            self::Weekly => 5,
        };
    }

    /**
     * How far back, in days, posts are considered for this cadence.
     */
    public function windowDays(): int
    {
        return match ($this) {
            self::Daily => 1,
            self::TwiceWeekly => 4,
            self::Weekly => 7,
        };
    }

    public function isDueOn(CarbonInterface $date): bool
    {
        return match ($this) {
            self::Daily => true,
            self::TwiceWeekly => $date->isMonday() || $date->isThursday(),
            self::Weekly => $date->isMonday(),
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
