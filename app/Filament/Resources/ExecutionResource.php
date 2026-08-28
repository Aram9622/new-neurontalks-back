<?php

namespace App\Filament\Resources;

use App\Filament\Forms\SeoSection;
use App\Filament\Resources\ExecutionResource\Pages;
use App\Models\Execution;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ExecutionResource extends Resource
{
    protected static ?string $model = Execution::class;

    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Main Content')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                        Forms\Components\TextInput::make('subtitle'),

                        Forms\Components\ColorPicker::make('color')
                            ->label('Brand Color')
                            ->placeholder('#000000'),

                        Forms\Components\TextInput::make('slug')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->dehydrated()
                            ->required()
                            ->unique(Execution::class, 'slug', ignoreRecord: true),
                    ])->columns(2),

                Forms\Components\Section::make('Details')
                    ->schema([
                        Forms\Components\FileUpload::make('image')->disk('public')
                            ->image()
                            ->directory('executions'),

                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                    ]),
                SeoSection::make()->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->disk('public'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\ColorColumn::make('color') // Визуальный показ цвета в таблице
                    ->label('Color'),
                Tables\Columns\TextColumn::make('subtitle'),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExecutions::route('/'),
            'create' => Pages\CreateExecution::route('/create'),
            'edit' => Pages\EditExecution::route('/{record}/edit'),
        ];
    }
}
