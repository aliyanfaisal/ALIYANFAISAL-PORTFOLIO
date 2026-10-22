<?php

namespace App\Filament\Resources\Newsletters\Pages;

use App\Enums\NewsletterFrequency;
use App\Mail\NewsletterMail;
use App\Models\BlogPost;
use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use App\Filament\Resources\Newsletters\NewsletterResource;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Builder;

class ListNewsletters extends ListRecords
{
    protected static string $resource = NewsletterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendTest')
                ->label('Send test to me')
                ->icon(Heroicon::OutlinedPaperAirplane)
                ->schema([
                    Select::make('frequency')
                        ->options(NewsletterFrequency::options())
                        ->default(NewsletterFrequency::Weekly->value)
                        ->required(),
                    TextInput::make('email')
                        ->email()
                        ->default(fn () => auth()->user()?->email)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $frequency = NewsletterFrequency::from($data['frequency']);

                    // Previews ignore delivery history: the most-viewed recent posts, or the newest ones if the window is empty.
                    $posts = BlogPost::published()
                        ->where('published_at', '>=', now()->subDays($frequency->windowDays()))
                        ->orderByDesc('views')->orderByDesc('published_at')
                        ->limit($frequency->postLimit())->get();

                    if ($posts->isEmpty()) {
                        $posts = BlogPost::published()->latest('published_at')->limit($frequency->postLimit())->get();
                    }

                    if ($posts->isEmpty()) {
                        Notification::make()->title('No published posts to preview')->warning()->send();

                        return;
                    }

                    $newsletter = new Newsletter(['frequency' => $frequency, 'subject' => '[Test] '.$posts->first()->title]);
                    $subscriber = NewsletterSubscriber::firstOrNew(['email' => $data['email']], ['frequency' => $frequency]);
                    $subscriber->token ??= 'preview';

                    Mail::to($data['email'])->send(new NewsletterMail($newsletter, $subscriber, $posts));

                    Notification::make()->title('Test sent to '.$data['email'])->success()->send();
                }),
        ];
    }

    public function getTabs(): array
    {
        $tabs = ['all' => Tab::make('All')];

        foreach (NewsletterFrequency::cases() as $frequency) {
            $tabs[$frequency->value] = Tab::make($frequency->label())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('frequency', $frequency->value));
        }

        return $tabs;
    }
}
