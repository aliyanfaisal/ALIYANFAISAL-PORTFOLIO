<?php

namespace App\Filament\Resources\BlogPosts\Tables;

use App\Filament\Resources\BlogPosts\Actions\SendToCuelaraAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BlogPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->disk('public')
                    ->label(''),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->slug),
                TextColumn::make('categories.name')
                    ->badge()
                    ->label('Categories'),
                TextColumn::make('tags.name')
                    ->badge()
                    ->color('gray')
                    ->label('Tags'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state, $record): string => $state === 'draft' && $record->published_at ? 'Scheduled' : ucfirst($state))
                    ->color(fn ($state, $record): string => $state === 'published' ? 'success' : ($record->published_at ? 'warning' : 'gray')),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('cuelara_synced_at')
                    ->label('Sent to Cuelara')
                    ->sortable()
                    ->badge()
                    ->icon(fn ($record): string => $record->cuelara_synced_at ? 'heroicon-o-check-circle' : 'heroicon-o-paper-airplane')
                    ->color(fn ($record): string => $record->cuelara_synced_at ? 'success' : 'primary')
                    ->default('Send to Cuelara')
                    ->formatStateUsing(fn ($record): string => $record->cuelara_synced_at?->format('M j, Y H:i') ?? 'Send to Cuelara')
                    ->tooltip(fn ($record): string => $record->cuelara_synced_at ? 'Click to resend' : 'Click to send')
                    ->action(SendToCuelaraAction::make()),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(['published' => 'Published', 'draft' => 'Draft']),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
