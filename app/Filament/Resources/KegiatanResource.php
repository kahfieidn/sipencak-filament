<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Periode;
use App\Models\Program;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Kegiatan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use App\Filament\Exports\KegiatanExporter;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Columns\Summarizers\Sum;
use App\Filament\Resources\KegiatanResource\Pages;
use App\Filament\Resources\KegiatanResource\RelationManagers\UserRelationManager;
use App\Filament\Resources\KegiatanResource\RelationManagers\SubKegiatanRelationManager;
use App\Filament\Resources\KegiatanResource\RelationManagers\SubKegiatansRelationManager;

class KegiatanResource extends Resource
{
    protected static ?string $model = Kegiatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Activity';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Kegiatan')
                    ->description('The items you have selected for purchase')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->options(User::role('panel_user')->get()->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->nullable(),
                        Forms\Components\Select::make('periode_id')
                            ->relationship('periode', titleAttribute: 'year')
                            ->preload()
                            ->searchable()
                            ->live()
                            ->native(false)
                            ->afterStateUpdated(function (Set $set) {
                                $set('program_id', null);
                            })
                            ->required(),
                        // Forms\Components\TextInput::make('program.sisa_pagu')
                        //     ->reactive()
                        //     ->disabled()
                        //     ->default(fn (Get $get): ?float => Program::find($get('program_id'))->sisa_pagu ?? null)
                        //     ->numeric(),
                        Forms\Components\Select::make('program_id')
                        ->options(fn (Get $get): Collection => Program::query()
                            ->where('periode_id', $get('periode_id'))
                            ->pluck('nama_program', 'id'))
                        ->searchable()
                        ->createOptionForm(function (Get $get) {
                            $periodeId = $get('periode_id'); // Get the current periode_id value
                            return [
                                Section::make('Program Baru')
                                    ->schema([
                                        Forms\Components\Select::make('periode_id')
                                            ->options(Periode::all()->pluck('year', 'id')->toArray())
                                            ->default($periodeId), // Set the default value to the current periode_id
                                        Forms\Components\TextInput::make('kode')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('nama_program')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('pagu')
                                            ->required()
                                            ->numeric(),
                                    ])->columns(2),
                            ];
                        })
                        ->createOptionUsing(function ($data) {
                            $group = new Program();
                            $group->periode_id = $data['periode_id'];
                            $group->kode = $data['kode'];
                            $group->nama_program = $data['nama_program'];
                            $group->pagu = $data['pagu'];
                            $group->save();
                            return $group->id;
                        })
                        ->live()
                        ->afterStateUpdated(function (Set $set, $state) {
                            $sisaPagu = Program::find($state)->sisa_pagu ?? null;
                            $set('program.sisa_pagu', $sisaPagu);
                        })
                        ->required(),
                        Forms\Components\Fieldset::make('Sisa Pagu Program')
                            ->relationship('program')
                            ->schema([
                                Forms\Components\TextInput::make('sisa_pagu'),
                            ]),
                        Forms\Components\TextInput::make('kode')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nama_kegiatan')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('pagu')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get, string $operation) {
                                if ($operation === 'edit') {
                                    dd("here");
                                } else if ($operation === 'create') {
                                    $program = Program::find($get('program_id'));
                                    if (!$program) {
                                        Notification::make()
                                            ->title('Program Wajib di Isi')
                                            ->body('Program harus di pilih terlebih dahulu.')
                                            ->danger()
                                            ->send();
                                        return;
                                    }
                                    if ($get('pagu') > $program->sisa_pagu) {
                                        $set('pagu', '');
                                        Notification::make()
                                            ->title('Pagu Kegiatan Melebihi Batas Program')
                                            ->body('Pagu tidak boleh melebihi dari pagu tahunan sebesar Rp.' . $program->pagu)
                                            ->danger()
                                            ->send();
                                    }
                                }

                                // $program = Program::find($get('program_id'));

                                // if (!$program) {
                                //     Notification::make()
                                //         ->title('Program Wajib di Isi')
                                //         ->body('Program harus di pilih terlebih dahulu.')
                                //         ->danger()
                                //         ->send();
                                //     return;
                                // }
                                // $jumlah_pagu_program = $program->kegiatan->sum('pagu');
                                // $pagu_kegiatan_sebelumnya = Kegiatan::where('id', $get('id'))->pluck('pagu')->first();

                                // if ($get('pagu') == null) {
                                //     $set('pagu', '');
                                // } else {
                                //     if ((($jumlah_pagu_program + $get('pagu') - $pagu_kegiatan_sebelumnya) > $program->pagu)) {
                                //         $set('pagu', $pagu_kegiatan_sebelumnya);
                                //         Notification::make()
                                //             ->title('Pagu Kegiatan Melebihi Batas')
                                //             ->body('Pagu tidak boleh melebihi dari pagu program sebesar Rp.' . $program->pagu)
                                //             ->danger()
                                //             ->send();
                                //     }
                                // }
                            })
                            ->columnSpanFull()
                            ->numeric(),
                    ])->columns(2)
                    ->collapsible()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_kegiatan')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\SelectColumn::make('user_id')
                    ->options(User::role('panel_user')->get()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pagu')
                    ->sortable()
                    ->summarize([
                        Sum::make()
                            ->label('Total Pagu')
                            ->money('Rp.')
                            ->numeric(decimalPlaces: 2),
                    ])->money('Rp.')
                    ->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('sisa_pagu')
                    ->sortable()
                    ->summarize([
                        Sum::make()
                            ->label('Total Sisa Pagu')
                            ->money('Rp.')
                            ->numeric(decimalPlaces: 2),
                    ])->money('Rp.')
                    ->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('status')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultGroup('program.nama_program')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ExportBulkAction::make()
                        ->exporter(KegiatanExporter::class),
                    Tables\Actions\BulkAction::make('Change Status')
                        ->icon('heroicon-m-lock-closed')
                        ->requiresConfirmation()
                        ->form([
                            Select::make('status')
                                ->options([
                                    'Dibuka' => 'Dibuka',
                                    'Dikunci' => 'Dikunci',
                                ])
                        ])
                        ->action(function (Collection $records, array $data) {
                            return $records->each(function ($record) use ($data) {
                                $id = $record->id;
                                Kegiatan::where('id', $id)->update(['status' => $data['status']]);
                            });
                        }),

                ]),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            SubKegiatanRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKegiatans::route('/'),
            'create' => Pages\CreateKegiatan::route('/create'),
            'edit' => Pages\EditKegiatan::route('/{record}/edit'),
        ];
    }
}
