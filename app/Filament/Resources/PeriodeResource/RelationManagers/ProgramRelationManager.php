<?php

namespace App\Filament\Resources\PeriodeResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Periode;
use App\Models\Program;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PeriodeResource;
use App\Filament\Resources\PeriodeResource\Pages\EditPeriode;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;


class ProgramRelationManager extends RelationManager
{
    protected static string $relationship = 'programs';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id')
                    ->hiddenOn('create', 'edit'),
                Forms\Components\TextInput::make('kode')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('periode_id')
                    ->relationship('periode', 'year')
                    ->hiddenOn('create', 'edit'),
                Forms\Components\TextInput::make('nama_program')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('pagu')
                    ->required()
                    ->reactive()
                    ->numeric()
                    ->afterStateUpdated(function ($state, $set, $get, string $operation) {
                        if ($operation === 'edit') {
                            $program = Program::find($get('id'));
                            if (!$program) {
                                Notification::make()
                                    ->title('Program Wajib di Isi')
                                    ->body('Program harus di pilih terlebih dahulu.')
                                    ->danger()
                                    ->send();
                                return;
                            }
                            $periode = $program->periode; // 52rb
                            $pagu_program_sebelumnya = Program::where('id', $get('id'))->pluck('pagu')->first();
                            $jumlah_batasan_pagu_program_exclude = Program::where('periode_id', $periode->id)
                                ->where('id', '!=', $program->id)
                                ->sum('pagu');
                            if ($get('pagu') == null) {
                                $set('pagu', '');
                            } else {
                                if (($jumlah_batasan_pagu_program_exclude + $get('pagu')) > $periode->batasan_pagu) {
                                    $set('pagu', $pagu_program_sebelumnya);
                                    Notification::make()
                                        ->title('Pagu Kegiatan Melebihi Batas Program')
                                        ->body('Pagu tidak boleh melebihi dari pagu tahunan sebesar Rp.' . $periode->batasan_pagu)
                                        ->danger()
                                        ->send();
                                }
                            }
                        } else if ($operation === 'create') {
                            if ($get('pagu') > $this->getOwnerRecord()->batasan_pagu) {
                                $set('pagu', '');
                                Notification::make()
                                    ->title('Pagu Kegiatan Melebihi Batas Program')
                                    ->body('Pagu tidak boleh melebihi dari pagu tahunan sebesar Rp.' . $this->getOwnerRecord()->batasan_pagu)
                                    ->danger()
                                    ->send();
                            };
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
                Tables\Actions\CreateAction::make()
                    ->after(function ($livewire) {
                        $jumlah_pagu_program = Program::where('periode_id', $this->getOwnerRecord()->id)
                            ->sum('pagu');
                        $this->getOwnerRecord()->update([
                            'sisa_pagu' => $this->getOwnerRecord()->batasan_pagu - $jumlah_pagu_program
                        ]);
                        $livewire->dispatch('refreshRelation')->to(EditPeriode::class);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($livewire) {
                        $jumlah_pagu_program = Program::where('periode_id', $this->getOwnerRecord()->id)
                            ->sum('pagu');
                        $this->getOwnerRecord()->update([
                            'sisa_pagu' => $this->getOwnerRecord()->batasan_pagu - $jumlah_pagu_program
                        ]);
                        $livewire->dispatch('refreshRelation')->to(EditPeriode::class);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($livewire) {
                        $jumlah_pagu_program = Program::where('periode_id', $this->getOwnerRecord()->id)
                            ->sum('pagu');
                        $this->getOwnerRecord()->update([
                            'sisa_pagu' => $this->getOwnerRecord()->batasan_pagu - $jumlah_pagu_program
                        ]);
                        $livewire->dispatch('refreshRelation')->to(EditPeriode::class);
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
