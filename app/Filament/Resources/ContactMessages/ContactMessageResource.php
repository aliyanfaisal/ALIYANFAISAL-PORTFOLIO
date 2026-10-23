<?php

namespace App\Filament\Resources\ContactMessages;

use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Filament\Resources\ContactMessages\Pages\ViewContactMessage;
use App\Filament\Resources\ContactMessages\Tables\ContactMessagesTable;
use App\Models\ContactMessage;
use BackedEnum;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?int $navigationSort = 19;

    public static function getNavigationBadge(): ?string
    {
        $unread = ContactMessage::whereNull('read_at')->count();

        return $unread ? (string) $unread : null;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Message')
                ->columns(2)
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('email')
                        ->copyable()
                        ->url(fn (ContactMessage $record) => 'mailto:'.$record->email),
                    TextEntry::make('subject')->placeholder('No subject'),
                    TextEntry::make('created_at')->label('Received')->dateTime(),
                    TextEntry::make('message')
                        ->columnSpanFull()
                        ->prose()
                        ->extraAttributes(['class' => 'whitespace-pre-line']),
                ]),
            Section::make('Replies')
                ->visible(fn (ContactMessage $record) => $record->replies()->exists())
                ->schema([
                    RepeatableEntry::make('replies')
                        ->hiddenLabel()
                        ->schema([
                            TextEntry::make('subject'),
                            TextEntry::make('created_at')->label('Sent')->dateTime(),
                            TextEntry::make('body')->label('Message')->columnSpanFull()->extraAttributes(['class' => 'whitespace-pre-line']),
                        ])
                        ->columns(2),
                ]),
            Section::make('Sender details')
                ->columns(2)
                ->schema([
                    TextEntry::make('ip_address')->label('IP address')->copyable()->placeholder('Not recorded'),
                    TextEntry::make('location')
                        ->state(fn (ContactMessage $record) => collect([$record->city, $record->countryName()])->filter()->implode(', ') ?: null)
                        ->placeholder('Not available'),
                    TextEntry::make('page_url')->label('Submitted from')->placeholder('Not recorded')->columnSpanFull(),
                    TextEntry::make('user_agent')->label('Browser / device')->placeholder('Not recorded')->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return ContactMessagesTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactMessages::route('/'),
            'view' => ViewContactMessage::route('/{record}'),
        ];
    }
}
