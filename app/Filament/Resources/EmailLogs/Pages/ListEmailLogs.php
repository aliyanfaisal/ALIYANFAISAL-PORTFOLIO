<?php

namespace App\Filament\Resources\EmailLogs\Pages;

use App\Filament\Resources\EmailLogs\EmailLogResource;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListEmailLogs extends ListRecords
{
    protected static string $resource = EmailLogResource::class;

    protected function getHeaderActions(): array
    {
        $enabled = Setting::current()->email_logging_enabled;

        return [
            Action::make('toggleLogging')
                ->label($enabled ? 'Logging is on — turn off' : 'Logging is off — turn on')
                ->icon($enabled ? Heroicon::OutlinedPause : Heroicon::OutlinedPlay)
                ->color($enabled ? 'success' : 'gray')
                ->requiresConfirmation($enabled)
                ->modalHeading('Turn off email logging?')
                ->modalDescription('Emails keep sending as normal, but new ones will no longer be recorded here. Existing entries are kept.')
                ->action(function (): void {
                    $settings = Setting::current();
                    $settings->update(['email_logging_enabled' => ! $settings->email_logging_enabled]);

                    Notification::make()
                        ->title($settings->email_logging_enabled ? 'Email logging turned on' : 'Email logging turned off')
                        ->success()
                        ->send();
                }),
        ];
    }
}
