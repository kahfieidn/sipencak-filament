<?php

namespace App\Filament\Resources\KegiatanResource\Pages;

use App\Filament\Resources\KegiatanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKegiatan extends CreateRecord
{
    protected static string $resource = KegiatanResource::class;

    protected function afterCreate(): void
    {
        $this->record->update([
            'sisa_pagu' => $this->record->pagu
        ]);

        $this->record->program->update([
            'sisa_pagu' => $this->record->program->sisa_pagu - $this->record->pagu
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
