<?php

namespace App\Filament\Resources\PeriodeResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Program;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

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
                Forms\Components\TextInput::make('nama_program')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('pagu')
                    ->required()
                    ->reactive()
                    ->numeric()
                    ->afterStateUpdated(function ($state, $set, $get) {

                        $program = Program::find($get('id'));

                        $periode = $program->periode; // 52rb
                        // $program = Program::find($get('program_id'));
                        // if (!$program) {
                        //     Notification::make()
                        //         ->title('Program Wajib di Isi')
                        //         ->body('Program harus di pilih terlebih dahulu.')
                        //         ->danger()
                        //         ->send();
                        //     return;
                        // }
                        $jumlah_pagu_program_sebelumnya = Program::where('periode_id', $periode->id)->sum('pagu');
                        dd($jumlah_pagu_program_sebelumnya + $get('pagu'));
                        // $pagu_kegiatan_sebelumnya = Kegiatan::where('id', $get('id'))->pluck('pagu')->first();
                        if ($get('pagu') == null) {
                            $set('pagu', '');
                        } else {
                            // if (($jumlah_pagu_periode + $get('pagu') > $periode->batasan_pagu)) {
                                // $set('pagu', $pagu_kegiatan_sebelumnya);
                                // Notification::make()
                                //     ->title('Pagu Kegiatan Melebihi Batas')
                                //     ->body('Pagu tidak boleh melebihi dari pagu program sebesar Rp.' . $program->pagu)
                                //     ->danger()
                                //     ->send();
                            // }
                        }
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
