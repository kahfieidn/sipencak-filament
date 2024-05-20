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
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\Summarizers\Average;
use App\Filament\Resources\KegiatanResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KegiatanResource\RelationManagers;

class KegiatanResource extends Resource
{
    protected static ?string $model = Kegiatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
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
                    ->required(),
                Forms\Components\TextInput::make('kode')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama_kegiatan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('pagu')
                    ->required()
                    ->maxLength(255),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_kegiatan')
                    ->sortable(),
                Tables\Columns\SelectColumn::make('user_id')
                    ->options(User::role('panel_user')->get()->pluck('name', 'id')->toArray())
                    ->sortable(),
                Tables\Columns\TextColumn::make('pagu')
                    ->sortable()
                    ->summarize([
                        Sum::make()
                        ->label('Total Pagu'),
                    ]),
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
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKegiatans::route('/'),
            // 'create' => Pages\CreateKegiatan::route('/create'),
            'edit' => Pages\EditKegiatan::route('/{record}/edit'),
        ];
    }
}
