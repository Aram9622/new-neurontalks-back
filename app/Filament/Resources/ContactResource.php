<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Mail\ContactReplyMail;
use App\Models\Contact;
use App\Models\MailTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Inquiry')
                    ->schema([
                        Forms\Components\TextInput::make('name')->disabled(),
                        Forms\Components\TextInput::make('email')->disabled(),
                        Forms\Components\TextInput::make('phone')->disabled(),
                        Forms\Components\Textarea::make('message')->disabled()->columnSpanFull(),
                    ])->columns(3),

                Forms\Components\Section::make('Your Reply')
                    ->schema([
                        Forms\Components\Textarea::make('reply')
                            ->placeholder('Type your answer here...'),
                        Forms\Components\DateTimePicker::make('replied_at')
                            ->disabled()
                            ->dehydrated(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\IconColumn::make('replied_at')
                    ->label('Replied')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->replied_at !== null),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Sent At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('not_replied')
                    ->query(fn ($query) => $query->whereNull('replied_at')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Reply')
                    ->action(function (Contact $record, array $data) {
                        // Если ответ ввели только сейчас
                        if (!empty($data['reply']) && empty($record->replied_at)) {
                            // Отправляем письмо пользователю напрямую
                            Mail::to($record->email)->send(new ContactReplyMail(
                                $record,
                                $data['reply'],
                                MailTemplate::preferredFor(MailTemplate::TYPE_CONTACT_REPLY),
                            ));
                            
                            $data['replied_at'] = now();
                        }
                        
                        $record->update($data);

                        Notification::make()
                            ->title('Reply Sent')
                            ->body('Your message has been sent to ' . $record->email)
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('send_reply')
                    ->label('Send Reply')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->form([
                        Forms\Components\Textarea::make('reply')->required(),
                    ])
                    ->action(function (Contact $record, array $data) {
                        // Отправляем письмо
                        Mail::to($record->email)->send(new ContactReplyMail(
                            $record,
                            $data['reply'],
                            MailTemplate::preferredFor(MailTemplate::TYPE_CONTACT_REPLY),
                        ));

                        $record->update([
                            'reply' => $data['reply'],
                            'replied_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Reply Sent')
                            ->body('The reply has been sent to ' . $record->email)
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->replied_at === null),
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
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
