<?php

namespace App\Filament\Resources\PeriodeResource\Pages;

use App\Filament\Resources\PeriodeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Livewire\Attributes\On;

class EditPeriode extends EditRecord
{
    protected static string $resource = PeriodeResource::class;
    protected $listeners = ['refreshRelation' => 'refresh'];

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function refresh(){
        $this->fillForm();
    }

}
