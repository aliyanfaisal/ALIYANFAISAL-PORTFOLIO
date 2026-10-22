<?php

namespace App\Filament\Resources\Comments\Tables;

use App\Models\Comment;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class CommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['blogPost', 'parent']))
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->description(fn (Comment $record) => $record->email),
                TextColumn::make('body')
                    ->label('Comment')
                    ->wrap()
                    ->limit(160)
                    ->tooltip(fn (Comment $record) => $record->body)
                    ->searchable(),
                TextColumn::make('blogPost.title')
                    ->label('Post')
                    ->limit(40)
                    ->url(fn (Comment $record) => $record->blogPost ? route('blog.show', $record->blogPost).'#comment-'.$record->id : null)
                    ->openUrlInNewTab()
                    ->searchable(),
                TextColumn::make('parent_id')
                    ->label('Type')
                    ->badge()
                    ->state(fn (Comment $record) => $record->parent_id ? 'Reply' : 'Comment')
                    ->color('gray'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->color(fn (string $state) => match ($state) {
                        Comment::STATUS_APPROVED => 'success',
                        Comment::STATUS_REJECTED => 'danger',
                        default => 'warning',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Posted')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    Comment::STATUS_PENDING => 'Pending',
                    Comment::STATUS_APPROVED => 'Approved',
                    Comment::STATUS_REJECTED => 'Rejected',
                ]),
                SelectFilter::make('blog_post_id')
                    ->label('Post')
                    ->relationship('blogPost', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Comment $record) => ! $record->isApproved())
                    ->action(fn (Comment $record) => $record->update(['status' => Comment::STATUS_APPROVED])),
                Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->visible(fn (Comment $record) => $record->status !== Comment::STATUS_REJECTED)
                    ->requiresConfirmation()
                    ->action(fn (Comment $record) => $record->update(['status' => Comment::STATUS_REJECTED])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['status' => Comment::STATUS_APPROVED]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['status' => Comment::STATUS_REJECTED]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
