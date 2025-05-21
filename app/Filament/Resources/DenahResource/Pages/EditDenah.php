<?php

namespace App\Filament\Resources\DenahResource\Pages;

use App\Filament\Resources\DenahResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDenah extends EditRecord
{
    protected static string $resource = DenahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
