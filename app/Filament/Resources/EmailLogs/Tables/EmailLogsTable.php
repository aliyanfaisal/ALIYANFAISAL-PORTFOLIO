<?php

namespace App\Filament\Resources\EmailLogs\Tables;

use App\Models\EmailLog;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EmailLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('to')
                    ->label('To')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('subject')
                    ->searchable()
                    ->limit(60)
                    ->tooltip(fn (EmailLog $record) => $record->subject)
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->badge()
                    ->state(fn (EmailLog $record) => $record->displayStatus())
                    ->color(fn (string $state) => match ($state) {
                        'sent' => 'success',
                        'failed' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(['sent' => 'Sent', 'sending' => 'Sending / not confirmed'])
                    ->query(fn ($query, array $data) => match ($data['value'] ?? null) {
                        'sent' => $query->where('status', 'sent'),
                        'sending' => $query->where('status', 'sending'),
                        default => $query,
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
