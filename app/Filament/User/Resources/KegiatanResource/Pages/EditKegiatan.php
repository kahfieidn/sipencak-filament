<?php

namespace App\Filament\User\Resources\KegiatanResource\Pages;

use App\Filament\User\Resources\KegiatanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKegiatan extends EditRecord
{
    protected static string $resource = KegiatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
