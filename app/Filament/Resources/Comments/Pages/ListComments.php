<?php

namespace App\Filament\Resources\Comments\Pages;

use App\Filament\Resources\Comments\CommentResource;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListComments extends ListRecords
{
    protected static string $resource = CommentResource::class;

    protected function getHeaderActions(): array
    {
        $auto = Setting::current()->auto_approve_comments;

        return [
            Action::make('approvalMode')
                ->label($auto ? 'Approval: Automatic' : 'Approval: Admin review')
                ->icon($auto ? 'heroicon-o-bolt' : 'heroicon-o-shield-check')
                ->color($auto ? 'success' : 'warning')
                ->modalHeading('Comment approval')
                ->modalDescription('Comments that fail the content filter (scripts, links, abusive words, spam) are always rejected with an error, in either mode.')
                ->modalSubmitActionLabel('Save')
                ->fillForm(fn () => ['mode' => Setting::current()->auto_approve_comments ? 'auto' : 'admin'])
                ->schema([
                    Radio::make('mode')
                        ->hiddenLabel()
                        ->options([
                            'auto' => 'Auto approval — comments go live immediately',
                            'admin' => 'Admin approval — comments stay hidden until you approve them',
                        ])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $setting = Setting::current();
                    $setting->update(['auto_approve_comments' => $data['mode'] === 'auto']);

                    Notification::make()
                        ->title($data['mode'] === 'auto' ? 'New comments are now auto-approved' : 'New comments now require admin approval')
                        ->success()
                        ->send();
                }),
        ];
    }
}
