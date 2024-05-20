<?php

namespace App\Filament\Resources\KegiatanResource\Pages;

use Closure;
use Filament\Actions;
use App\Models\Kegiatan;

use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Exports\ExportColumn;
use App\Filament\Resources\KegiatanResource;


class ListKegiatans extends ListRecords
{
    protected static string $resource = KegiatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            '2024' => Tab::make()->modifyQueryUsing(function (Builder $query) {
                $query->where('periode_id', 1);
            }),
            '2025' => Tab::make()->modifyQueryUsing(function (Builder $query) {
                $query->where('periode_id', 2);
            }),
            '2026' => Tab::make()->modifyQueryUsing(function (Builder $query) {
                $query->where('periode_id', 3);
            }),
        ];
    }
}
