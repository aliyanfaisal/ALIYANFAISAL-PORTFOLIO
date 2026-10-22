<?php

namespace App\Filament\Resources\NewsletterSubscribers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use App\Enums\NewsletterFrequency;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NewsletterSubscribersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('frequency')
                    ->badge()
                    ->formatStateUsing(fn (NewsletterFrequency $state) => $state->label()),
                TextColumn::make('unsubscribed_at')
                    ->label('Status')
                    ->badge()
                    ->state(fn ($record) => $record->isSubscribed() ? 'Subscribed' : 'Unsubscribed')
                    ->color(fn (string $state) => $state === 'Subscribed' ? 'success' : 'gray'),
                TextColumn::make('last_sent_at')
                    ->label('Last emailed')
                    ->dateTime()
                    ->placeholder('Never')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Subscribed')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('frequency')->options(NewsletterFrequency::options()),
                Filter::make('unsubscribed')
                    ->label('Unsubscribed only')
                    ->query(fn ($query) => $query->whereNotNull('unsubscribed_at')),
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
