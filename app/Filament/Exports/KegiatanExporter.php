<?php

namespace App\Filament\Exports;

use App\Models\Kegiatan;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class KegiatanExporter extends Exporter
{
    protected static ?string $model = Kegiatan::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('program.nama_program'),
            ExportColumn::make('periode.year'),
            ExportColumn::make('user.name'),
            ExportColumn::make('kode'),
            ExportColumn::make('nama_kegiatan'),
            ExportColumn::make('pagu'),
            ExportColumn::make('status'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your kegiatan export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
