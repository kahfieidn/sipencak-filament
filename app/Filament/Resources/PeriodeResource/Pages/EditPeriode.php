<?php

namespace App\Filament\Resources\PeriodeResource\Pages;

use Filament\Actions;
use Livewire\Livewire;
use App\Models\Periode;
use Livewire\Attributes\On;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\PeriodeResource;

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

    protected function afterSave(): void
    {
        $periode = Periode::find($this->record->id);
        $jumlah_pagu_program = $periode->programs->sum('pagu');
        $periode->update([
            'sisa_pagu' => $periode->batasan_pagu - $jumlah_pagu_program
        ]);
        $this->dispatch('refreshRelation');

    }
    

    public function refresh()
    {
        $this->fillForm();
    }
}
