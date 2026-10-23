<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Mail\ContactReplyMail;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;
use Throwable;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->getRecord()->read_at === null) {
            $this->getRecord()->update(['read_at' => now()]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reply')
                ->label('Reply')
                ->icon(Heroicon::OutlinedPaperAirplane)
                ->modalHeading(fn (ContactMessage $record) => 'Reply to '.$record->name)
                ->modalDescription(fn (ContactMessage $record) => 'Sent to '.$record->email.'. Their original message is quoted below your reply.')
                ->modalSubmitActionLabel('Send reply')
                ->fillForm(fn (ContactMessage $record) => [
                    'subject' => 'Re: '.($record->subject ?: 'Your message'),
                ])
                ->schema([
                    TextInput::make('subject')->required()->maxLength(255),
                    Textarea::make('body')->label('Message')->required()->rows(8)->maxLength(10000),
                ])
                ->action(function (array $data, ContactMessage $record): void {
                    $reply = $record->replies()->create($data);

                    try {
                        Mail::to($record->email)->send(new ContactReplyMail($reply));
                    } catch (Throwable $exception) {
                        $reply->delete();
                        report($exception);

                        Notification::make()->title('Reply could not be sent')->body('Check the mail settings and try again.')->danger()->send();

                        return;
                    }

                    Notification::make()->title('Reply sent to '.$record->email)->success()->send();
                }),
            DeleteAction::make(),
        ];
    }
}
