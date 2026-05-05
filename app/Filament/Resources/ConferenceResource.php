<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConferenceResource\Pages;
use App\Models\Conference;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ConferenceResource extends Resource
{
    protected static ?string $model = Conference::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Conference Details')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General Info')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                                        $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                Forms\Components\TextInput::make('slug')
                                    ->disabled(fn (string $operation) => $operation === 'edit')
                                    ->dehydrated()
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                Forms\Components\TextInput::make('subtitle'),
                                Forms\Components\Textarea::make('description')
                                    ->columnSpanFull(),
                                Forms\Components\DateTimePicker::make('date'),
                                Forms\Components\TextInput::make('location'),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('Media & Buttons')
                            ->schema([
                                Forms\Components\FileUpload::make('main_image')
                                    ->image()
                                    ->directory('conferences/images'),
                                Forms\Components\TextInput::make('video_url')
                                    ->url()
                                    ->placeholder('YouTube or Vimeo URL'),
                                Forms\Components\TextInput::make('button_title'),
                                Forms\Components\TextInput::make('button_link')
                                    ->url(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Agenda')
                            ->schema([
                                Forms\Components\Repeater::make('agendas')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\TextInput::make('name')->required(),
                                        Forms\Components\TextInput::make('icon'),
                                        Forms\Components\TimePicker::make('time_from'),
                                        Forms\Components\TimePicker::make('time_to'),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Sections')
                            ->schema([
                                Forms\Components\Repeater::make('sections')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\TextInput::make('title')->required(),
                                        Forms\Components\Textarea::make('description'),
                                        Forms\Components\FileUpload::make('image')
                                            ->image()
                                            ->directory('conferences/sections'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Speakers & Partners')
                            ->schema([
                                Forms\Components\Select::make('speakers')
                                    ->relationship('speakers', 'fullname')
                                    ->multiple()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('fullname')->required(),
                                        Forms\Components\TextInput::make('profession'),
                                        Forms\Components\FileUpload::make('image')->image(),
                                    ]),
                                Forms\Components\Select::make('partners')
                                    ->relationship('partners', 'name')
                                    ->multiple()
                                    ->preload(),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('main_image'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location'),
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
            'index' => Pages\ListConferences::route('/'),
            'create' => Pages\CreateConference::route('/create'),
            'edit' => Pages\EditConference::route('/{record}/edit'),
        ];
    }
}
