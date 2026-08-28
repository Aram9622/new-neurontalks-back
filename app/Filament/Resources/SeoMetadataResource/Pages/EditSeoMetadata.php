<?php

namespace App\Filament\Resources\SeoMetadataResource\Pages;

use App\Filament\Resources\SeoMetadataResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeoMetadata extends EditRecord
{
    protected static string $resource = SeoMetadataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
