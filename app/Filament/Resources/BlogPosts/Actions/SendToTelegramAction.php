<?php

namespace App\Filament\Resources\BlogPosts\Actions;

use App\Jobs\PostBlogPostToTelegram;
use App\Models\BlogPost;
use App\Services\TelegramService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class SendToTelegramAction
{
    public static function make(): Action
    {
        return Action::make('sendToTelegram')
            ->label(fn (BlogPost $record): string => $record->telegram_posted_at ? 'Repost to Telegram' : 'Post to Telegram')
            ->icon('heroicon-o-paper-airplane')
            ->visible(fn (BlogPost $record): bool => $record->status === 'published')
            ->requiresConfirmation()
            ->modalDescription(fn (BlogPost $record): string => $record->telegram_posted_at
                ? 'Already posted on '.$record->telegram_posted_at->toDayDateTimeString().'. This will post it again.'
                : 'Post this to the Telegram channel now.')
            ->action(function (BlogPost $record): void {
                if (! app(TelegramService::class)->isConfigured()) {
                    Notification::make()
                        ->title('Telegram is not configured')
                        ->body('Set TELEGRAM_BOT_TOKEN in the environment and the channel in Settings.')
                        ->danger()
                        ->send();

                    return;
                }

                PostBlogPostToTelegram::dispatch($record, force: true);

                Notification::make()->title('Post queued for Telegram')->success()->send();
            });
    }
}
