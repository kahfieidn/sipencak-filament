<?php

namespace App\Filament\Resources\KegiatanResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\KegiatanResource;

class EditKegiatan extends EditRecord
{
    protected static string $resource = KegiatanResource::class;
    protected $listeners = ['refresh' => 'refreshForm'];

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Mencari program berdasarkan program_id dari data
        $program = \App\Models\Program::find($data['program_id']);
    
        // Memeriksa apakah nilai pagu dari data lebih besar dari nilai pagu program
        if ($data['pagu'] > $program->pagu) {
            // Membuat notifikasi kesalahan jika nilai pagu tidak valid
            Notification::make()
                ->title('Error')
                ->body('Pagu tidak boleh melebihi dari pagu program sebesar' . $program->pagu)
                ->danger()
                ->send();
    
            // Menghentikan eksekusi dan mengembalikan data kosong atau nilai yang tidak valid
            throw new \Exception('Pagu tidak boleh melebihi dari pagu program.');
        }
    
        return $data;
    }



    public function refreshForm()
    {
        $this->fillForm();
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
