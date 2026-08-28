<?php

namespace App\Filament\Resources;

use App\Filament\Forms\SeoSection;
use App\Filament\Resources\SeoMetadataResource\Pages;
use App\Models\SeoMetadata;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class SeoMetadataResource extends Resource
{
    protected static ?string $model = SeoMetadata::class;

    protected static ?string $navigationGroup = 'SEO';

    protected static ?string $navigationLabel = 'SEO audit';

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $modelLabel = 'SEO metadata';

    public static function form(Form $form): Form
    {
        return $form->schema(SeoSection::schema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('seoable'))
            ->columns([
                Tables\Columns\TextColumn::make('seoable_type')
                    ->label('Content type')
                    ->formatStateUsing(fn (string $state): string => Str::headline(class_basename($state)))
                    ->badge(),
                Tables\Columns\TextColumn::make('seoable.title')
                    ->label('Content'),
                Tables\Columns\TextColumn::make('title')
                    ->label('SEO title')
                    ->placeholder('Missing')
                    ->searchable(),
                Tables\Columns\TextColumn::make('score')
                    ->label('Score')
                    ->state(fn (SeoMetadata $record): string => $record->score() . '%')
                    ->badge()
                    ->color(fn (SeoMetadata $record): string => match (true) {
                        $record->score() >= 80 => 'success',
                        $record->score() >= 40 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\IconColumn::make('robots_index')
                    ->label('Indexed')
                    ->boolean(),
                Tables\Columns\IconColumn::make('include_in_sitemap')
                    ->label('Sitemap')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('robots_index')
                    ->label('Indexing'),
                Tables\Filters\TernaryFilter::make('include_in_sitemap')
                    ->label('Included in sitemap'),
                Tables\Filters\Filter::make('incomplete')
                    ->label('Missing important metadata')
                    ->query(fn (Builder $query): Builder => $query->where(fn (Builder $query) => $query
                        ->whereNull('title')
                        ->orWhereNull('description')
                        ->orWhereNull('og_image'))),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeoMetadata::route('/'),
            'edit' => Pages\EditSeoMetadata::route('/{record}/edit'),
        ];
    }
}
