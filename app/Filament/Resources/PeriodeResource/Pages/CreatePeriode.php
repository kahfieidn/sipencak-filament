<?php

namespace App\Filament\Resources\PeriodeResource\Pages;

use Filament\Actions;
use App\Models\Program;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PeriodeResource;

class CreatePeriode extends CreateRecord
{
    protected static string $resource = PeriodeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['sisa_pagu'] = $data['batasan_pagu'];

        return $data;
    }
}
