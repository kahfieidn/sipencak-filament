<?php

namespace App\Filament\User\Resources\KegiatanResource\Pages;

use App\Filament\User\Resources\KegiatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKegiatans extends ListRecords
{
    protected static string $resource = KegiatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
