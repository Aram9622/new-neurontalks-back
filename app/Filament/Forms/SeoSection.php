<?php

namespace App\Filament\Forms;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Illuminate\Support\Str;

class SeoSection
{
    public static function make(): Section
    {
        return Section::make('SEO')
            ->description('Search result, social sharing, indexing and structured data settings.')
            ->icon('heroicon-o-magnifying-glass')
            ->relationship('seo')
            ->schema(self::schema())
            ->columns(2)
            ->collapsible()
            ->collapsed();
    }

    public static function schema(): array
    {
        return [
            Forms\Components\TextInput::make('title')
                ->label('SEO title')
                ->helperText(fn (?string $state): string => Str::length($state ?? '') . '/60 characters')
                ->maxLength(255)
                ->live(),
            Forms\Components\TextInput::make('canonical_url')
                ->label('Canonical URL')
                ->url()
                ->maxLength(255),
            Forms\Components\Textarea::make('description')
                ->label('Meta description')
                ->helperText(fn (?string $state): string => Str::length($state ?? '') . '/160 characters')
                ->rows(3)
                ->live()
                ->columnSpanFull(),
            Forms\Components\Toggle::make('robots_index')
                ->label('Allow indexing')
                ->default(true),
            Forms\Components\Toggle::make('robots_follow')
                ->label('Follow links')
                ->default(true),
            Forms\Components\TextInput::make('og_title')
                ->label('Open Graph title')
                ->maxLength(255),
            Forms\Components\Textarea::make('og_description')
                ->label('Open Graph description')
                ->rows(3),
            Forms\Components\FileUpload::make('og_image')
                ->label('Social sharing image')
                ->disk('public')
                ->directory('seo')
                ->image()
                ->imageEditor()
                ->columnSpanFull(),
            Forms\Components\Select::make('twitter_card')
                ->options([
                    'summary' => 'Summary',
                    'summary_large_image' => 'Summary with large image',
                ])
                ->default('summary_large_image'),
            Forms\Components\Select::make('schema_type')
                ->label('Structured data type')
                ->options([
                    'Article' => 'Article',
                    'NewsArticle' => 'News article',
                    'Service' => 'Service',
                    'Event' => 'Event',
                    'CreativeWork' => 'Creative work',
                ]),
            Forms\Components\KeyValue::make('schema_data')
                ->label('Additional structured data')
                ->keyLabel('Property')
                ->valueLabel('Value')
                ->columnSpanFull(),
            Forms\Components\Toggle::make('include_in_sitemap')
                ->label('Include in sitemap')
                ->default(true)
                ->columnSpanFull(),
        ];
    }
}
