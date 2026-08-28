<?php

namespace App\Filament\Resources\SeoMetadataResource\Pages;

use App\Filament\Resources\SeoMetadataResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListSeoMetadata extends ListRecords
{
    protected static string $resource = SeoMetadataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generateSitemap')
                ->label('Generate sitemap')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->action(function (): void {
                    Artisan::call('sitemap:generate');

                    Notification::make()
                        ->title('Sitemap generated')
                        ->body(url('/sitemap.xml'))
                        ->success()
                        ->send();
                }),
            Actions\Action::make('openSitemap')
                ->label('Open sitemap')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(url('/sitemap.xml'))
                ->openUrlInNewTab(),
        ];
    }
}
