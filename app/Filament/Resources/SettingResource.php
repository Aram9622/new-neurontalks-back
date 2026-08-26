<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Configuration';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Global Settings')
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->live()
                            ->placeholder('e.g. site_logo, phone, email'),

                        Forms\Components\Select::make('type')
                            ->options([
                                'text' => 'Text',
                                'image' => 'Image',
                            ])
                            ->default('text')
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('group')
                            ->options([
                                'general' => 'General',
                                'seo' => 'SEO',
                                'contact' => 'Contact',
                                'social' => 'Social',
                            ])
                            ->default('general')
                            ->required(),

                        // Поле напрямую привязано к колонке image_value
                        Forms\Components\FileUpload::make('image_value')->disk('public')
                            ->label('Upload Image')
                            ->image()
                            ->directory('settings')
                            ->visible(fn ($get) => $get('type') === 'image')
                            ->required(fn ($get) => $get('type') === 'image'),

                        // Поле напрямую привязано к колонке text_value
                        Forms\Components\Textarea::make('text_value')
                            ->label('Value')
                            ->rows(3)
                            ->visible(fn ($get) => $get('type') === 'text')
                            ->required(fn ($get) => $get('type') === 'text'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('group')
                    ->badge(),
                Tables\Columns\ImageColumn::make('image_value')->disk('public')
                    ->label('Preview'),
                Tables\Columns\TextColumn::make('text_value')
                    ->label('Value')
                    ->limit(50)
                    ->visible(fn ($record) => $record?->type === 'text'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->options([
                        'general' => 'General',
                        'seo' => 'SEO',
                        'contact' => 'Contact',
                        'social' => 'Social',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
