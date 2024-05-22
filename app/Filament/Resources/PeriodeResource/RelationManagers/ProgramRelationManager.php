<?php

namespace App\Filament\Resources\PeriodeResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Program;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use App\Models\Periode;

class ProgramRelationManager extends RelationManager
{
    protected static string $relationship = 'programs';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('periode_id')
                    ->relationship('periode', 'year')
                    ->hiddenOn('create','edit'),
                Forms\Components\TextInput::make('nama_program')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('pagu')
                    ->required()
                    ->reactive()
                    ->numeric()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        // dd($get('id'));
                        // $program = Program::find($get('id'));
                        // $periode = $program->periode; // 52rb
                        // // $program = Program::find($get('program_id'));
                        // // if (!$program) {
                        // //     Notification::make()
                        // //         ->title('Program Wajib di Isi')
                        // //         ->body('Program harus di pilih terlebih dahulu.')
                        // //         ->danger()
                        // //         ->send();
                        // //     return;
                        // // }
                        // $jumlah_pagu_program_sebelumnya = Program::where('periode_id', $periode->id)->sum('pagu');
                        // // $pagu_kegiatan_sebelumnya = Kegiatan::where('id', $get('id'))->pluck('pagu')->first();
                        // if ($get('pagu') == null) {
                        //     $set('pagu', '');
                        // } else {
                        //     if (($jumlah_pagu_program_sebelumnya + $get('pagu') - $program->pagu) > $periode->batasan_pagu) {
                        //         // dd($program->pagu);
                        //         $set('pagu', $program->pagu);
                        //         Notification::make()
                        //             ->title('Pagu Kegiatan Melebihi Batas Program')
                        //             ->body('Pagu tidak boleh melebihi dari pagu tahunan sebesar Rp.' . $periode->batasan_pagu)
                        //             ->danger()
                        //             ->send();
                        //     }
                        // }
                    }),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('kode')
            ->columns([
                Tables\Columns\TextColumn::make('kode'),
                Tables\Columns\TextColumn::make('nama_program'),
                Tables\Columns\TextColumn::make('pagu'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $periode = Periode::find($data['periode_id']);
                        $jumlah_batasan_pagu_update = Program::where('periode_id', $periode->id)->sum('pagu')->where('id', '!=', $data['id']);
                        dd($jumlah_batasan_pagu_update);
                        $periode->update([
                            'sisa_pagu' => $periode->batasan_pagu - $jumlah_batasan_pagu_update
                        ]);
                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
