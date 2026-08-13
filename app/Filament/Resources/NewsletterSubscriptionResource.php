<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsletterSubscriptionResource\Pages;
use App\Models\NewsletterSubscription;
use App\Models\MailTemplate;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NewsletterSubscriptionResource extends Resource
{
    protected static ?string $model = NewsletterSubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Subscribers';

    protected static ?string $navigationGroup = 'Customers';

    protected static ?string $modelLabel = 'subscriber';

    protected static ?string $pluralModelLabel = 'Subscribers';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Subscribed At')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_sent_at')
                    ->label('Last Newsletter Sent')
                    ->dateTime()
                    ->placeholder('Never')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mailTemplate.name')
                    ->label('Attached template')->placeholder('Default template'),
                Tables\Columns\TextColumn::make('latestDelivery.mailTemplate.name')
                    ->label('Last sent template')->placeholder('Built-in template'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('attachTemplate')
                    ->label('Attach template')->icon('heroicon-o-paper-clip')
                    ->form([
                        Forms\Components\Select::make('mail_template_id')->label('Mail template')
                            ->options(MailTemplate::query()->where('type', MailTemplate::TYPE_NEWSLETTER)->pluck('name', 'id'))
                            ->searchable()->nullable()->helperText('Leave empty to use the default template.'),
                    ])->fillForm(fn (NewsletterSubscription $record) => ['mail_template_id' => $record->mail_template_id])
                    ->action(fn (NewsletterSubscription $record, array $data) => $record->update($data)),
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
            'index' => Pages\ListNewsletterSubscriptions::route('/'),
        ];
    }
}
