<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use App\Models\Category;
use App\Models\Tag;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $state, callable $set, string $operation) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->alphaDash(),
                Textarea::make('excerpt')
                    ->maxLength(500)
                    ->helperText('Used as the meta description and social preview text. Leave blank to auto-generate from the body.')
                    ->columnSpanFull(),
                Textarea::make('body')
                    ->required()
                    ->rows(14)
                    ->helperText('Plain text or Markdown. Each blank line starts a new paragraph.')
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->label('Cover image')
                    ->image()
                    ->disk('public')
                    ->directory('blog'),
                Select::make('categories')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->unique(Category::class, 'slug'),
                    ]),
                Select::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->unique(Tag::class, 'slug'),
                    ]),
                Select::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published'])
                    ->default('draft')
                    ->required()
                    ->native(false)
                    ->helperText('Published goes live at the moment you save.'),
                DateTimePicker::make('published_at')
                    ->label('Scheduled / published at')
                    ->helperText('For a draft, set a future date and time to publish it automatically then. Set to Published, this shows when it went live.')
                    ->native(false),
                DateTimePicker::make('cuelara_synced_at')
                    ->label('Last sent to Cuelara')
                    ->placeholder('Not sent yet')
                    ->disabled()
                    ->dehydrated(false)
                    ->visibleOn('edit'),
            ]);
    }
}
