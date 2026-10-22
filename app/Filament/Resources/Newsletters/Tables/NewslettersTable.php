<?php

namespace App\Filament\Resources\Newsletters\Tables;

use App\Enums\NewsletterFrequency;
use App\Models\Newsletter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NewslettersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('posts:id,title'))
            ->columns([
                TextColumn::make('run_date')
                    ->label('Run')
                    ->date()
                    ->sortable(),
                TextColumn::make('frequency')
                    ->badge()
                    ->formatStateUsing(fn (NewsletterFrequency $state) => $state->label()),
                TextColumn::make('subject')
                    ->limit(50)
                    ->tooltip(fn (Newsletter $record) => $record->subject),
                TextColumn::make('posts.title')
                    ->label('Posts included')
                    ->listWithLineBreaks()
                    ->limit(45)
                    ->placeholder('—'),
                TextColumn::make('recipients_count')
                    ->label('Recipients')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'sent' => 'success',
                        'sending' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('run_date', 'desc');
    }
}
