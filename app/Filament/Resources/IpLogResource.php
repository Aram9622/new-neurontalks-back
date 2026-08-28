<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IpLogResource\Pages;
use App\Models\IpLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class IpLogResource extends Resource
{
    protected static ?string $model = IpLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationLabel = 'IP Logs';

    protected static ?string $navigationGroup = 'Customers';

    protected static ?string $modelLabel = 'IP log';

    protected static ?string $pluralModelLabel = 'IP Logs';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('method')->badge(),
                Tables\Columns\TextColumn::make('url')
                    ->limit(60)
                    ->tooltip(fn (IpLog $record): string => $record->url)
                    ->searchable(),
                Tables\Columns\TextColumn::make('referrer')
                    ->label('Referrer')
                    ->limit(40)
                    ->placeholder('Direct visit')
                    ->tooltip(fn (IpLog $record): ?string => $record->referrer),
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('Browser / Device')
                    ->limit(50)
                    ->placeholder('Unknown')
                    ->tooltip(fn (IpLog $record): ?string => $record->user_agent)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Visited At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListIpLogs::route('/'),
        ];
    }
}
