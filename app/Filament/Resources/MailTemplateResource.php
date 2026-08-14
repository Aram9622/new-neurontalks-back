<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MailTemplateResource\Pages;
use App\Forms\Components\MailTemplateEditor;
use App\Models\MailTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MailTemplateResource extends Resource
{
    protected static ?string $model = MailTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Configuration';
    protected static ?string $navigationLabel = 'Mail templates';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\Select::make('type')->options([
                MailTemplate::TYPE_NEWSLETTER => 'Subscribers newsletter',
                MailTemplate::TYPE_AUDIT => 'Audit request notification',
            ])->default(MailTemplate::TYPE_NEWSLETTER)->required(),
            Forms\Components\TextInput::make('subject')->required()
                ->helperText('Available placeholders: [[month]], [[name]], [[email]], [[phone]], [[message]], [[improve]].'),
            MailTemplateEditor::make('body')
                ->label('Body')
                ->required()
                ->columnSpanFull()
                ->helperText('Build the email visually, edit its HTML, or start from the ready-made layout. Available placeholders depend on the mail type.'),
            Forms\Components\Toggle::make('is_default')->label('Default for this mail type')
                ->helperText('Used when a subscriber has no template attached.'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('type')->badge(),
            Tables\Columns\TextColumn::make('subject')->limit(50),
            Tables\Columns\IconColumn::make('is_default')->boolean()->label('Default'),
            Tables\Columns\TextColumn::make('subscriptions_count')->counts('subscriptions')->label('Subscribers'),
        ])->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMailTemplates::route('/'),
            'create' => Pages\CreateMailTemplate::route('/create'),
            'edit' => Pages\EditMailTemplate::route('/{record}/edit'),
        ];
    }
}
