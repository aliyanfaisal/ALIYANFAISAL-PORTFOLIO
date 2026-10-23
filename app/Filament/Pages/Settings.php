<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\TelegramService;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class Settings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.settings';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Setting::current()->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Site')
                    ->schema([
                        TextInput::make('site_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('site_description')
                            ->label('Default meta description')
                            ->maxLength(500),
                        FileUpload::make('default_og_image')
                            ->label('Default social share image')
                            ->helperText('Used as the Open Graph image for blog posts that have no cover image of their own.')
                            ->image()
                            ->disk('public')
                            ->directory('settings'),
                        TextInput::make('contact_email')
                            ->label('Public contact email')
                            ->helperText('Shown on the site (footer, privacy policy).')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('admin_email')
                            ->label('Admin email')
                            ->helperText('Private. New contact messages and demo requests are emailed here. Leave empty to use the server default.')
                            ->email()
                            ->maxLength(255),
                    ]),
                Section::make('Blog API')
                    ->schema([
                        Toggle::make('auto_approve_posts')
                            ->label('Auto-approve posts from the API')
                            ->helperText('When on, posts submitted via POST /api/blog-posts go live immediately (or at their given "published_at"). When off, they are saved as drafts for you to review and publish manually from the Posts screen.')
                            ->default(true),
                    ]),
                Section::make('Telegram channel')
                    ->description('New posts are announced in your channel automatically. The bot token lives in the environment (TELEGRAM_BOT_TOKEN); the bot must be an admin of the channel.')
                    ->schema([
                        Toggle::make('telegram_enabled')
                            ->label('Auto-post new blog posts to Telegram')
                            ->helperText('Turning this off also cancels posts still waiting out a delay.')
                            ->default(true),
                        TextInput::make('telegram_chat_id')
                            ->label('Channel to post in')
                            ->placeholder('@yourchannel')
                            ->helperText('The channel username with @, or its numeric id.')
                            ->maxLength(255),
                        TextInput::make('telegram_delay_minutes')
                            ->label('Delay before posting (minutes)')
                            ->helperText('0 posts as soon as the blog post goes live.')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1440)
                            ->default(0)
                            ->required(),
                        Toggle::make('telegram_include_excerpt')
                            ->label('Include the excerpt')
                            ->default(true),
                        Toggle::make('telegram_include_hashtags')
                            ->label('Add category hashtags')
                            ->default(true),
                    ]),
                Section::make('Social links')
                    ->schema([
                        TextInput::make('github_url')
                            ->label('GitHub')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('linkedin_url')
                            ->label('LinkedIn')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('fiverr_url')
                            ->label('Fiverr')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('telegram_channel_url')
                            ->label('Telegram channel link')
                            ->placeholder('https://t.me/yourchannel')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('whatsapp_channel_url')
                            ->label('WhatsApp channel link')
                            ->placeholder('https://whatsapp.com/channel/...')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('upwork_url')
                            ->label('Upwork')
                            ->url()
                            ->maxLength(255),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testTelegram')
                ->label('Send Telegram test message')
                ->icon(Heroicon::OutlinedPaperAirplane)
                ->color('gray')
                ->action(function (): void {
                    $settings = Setting::current();
                    $telegram = app(TelegramService::class);

                    if (! $telegram->isConfigured($settings)) {
                        Notification::make()->title('Telegram is not configured')->body('Save a channel here and set TELEGRAM_BOT_TOKEN in the environment.')->danger()->send();

                        return;
                    }

                    try {
                        $telegram->sendMessage('Test message from '.e($settings->site_name).': Telegram posting is connected.', $settings);
                    } catch (\Throwable $exception) {
                        Notification::make()->title('Telegram rejected the message')->body($exception->getMessage())->danger()->send();

                        return;
                    }

                    Notification::make()->title('Test message sent')->success()->send();
                }),
        ];
    }

    public function save(): void
    {
        Setting::current()->update($this->form->getState());

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }
}
