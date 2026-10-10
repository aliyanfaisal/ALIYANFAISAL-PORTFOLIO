<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
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
                            ->helperText('Used as the Open Graph image when a page has no image of its own.')
                            ->image()
                            ->disk('public')
                            ->directory('settings'),
                        TextInput::make('contact_email')
                            ->email()
                            ->maxLength(255),
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
                        TextInput::make('upwork_url')
                            ->label('Upwork')
                            ->url()
                            ->maxLength(255),
                    ]),
            ])
            ->statePath('data');
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
