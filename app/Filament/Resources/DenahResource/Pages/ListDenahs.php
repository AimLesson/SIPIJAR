<?php

namespace App\Filament\Resources\DenahResource\Pages;

use App\Filament\Resources\DenahResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDenahs extends ListRecords
{
    protected static string $resource = DenahResource::class;

    protected function getHeaderActions(): array
    {
        if (\App\Models\Denah::count() > 0) {
            return [];
        }

        return [
            Actions\CreateAction::make(),
        ];
    }
}
