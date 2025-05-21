<?php

namespace App\Filament\Resources\DenahResource\Pages;

use App\Filament\Resources\DenahResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDenah extends ViewRecord
{
    protected static string $resource = DenahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
