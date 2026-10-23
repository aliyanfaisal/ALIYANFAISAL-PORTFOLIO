<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use App\Models\ContactMessage;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('read_at')
                    ->label('')
                    ->boolean()
                    ->trueIcon('heroicon-o-envelope-open')
                    ->falseIcon('heroicon-s-envelope')
                    ->trueColor('gray')
                    ->falseColor('primary')
                    ->tooltip(fn (ContactMessage $record) => $record->read_at ? 'Read' : 'Unread'),
                TextColumn::make('created_at')->label('Received')->dateTime()->sortable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable()->copyable(),
                TextColumn::make('subject')->searchable()->limit(50)->placeholder('No subject'),
                TextColumn::make('country')
                    ->label('Location')
                    ->state(fn (ContactMessage $record) => collect([$record->city, $record->countryName()])->filter()->implode(', ') ?: null)
                    ->placeholder('—'),
                TextColumn::make('ip_address')->label('IP')->searchable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (ContactMessage $record) => \App\Filament\Resources\ContactMessages\ContactMessageResource::getUrl('view', ['record' => $record]))
            ->filters([
                TernaryFilter::make('read_at')
                    ->label('Status')
                    ->placeholder('All')
                    ->trueLabel('Read')
                    ->falseLabel('Unread')
                    ->nullable(),
            ])
            ->recordActions([ViewAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
