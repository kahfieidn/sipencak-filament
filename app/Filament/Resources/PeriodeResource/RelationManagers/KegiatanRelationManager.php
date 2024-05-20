<?php

namespace App\Filament\Resources\PeriodeResource\RelationManagers;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Program;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class KegiatanRelationManager extends RelationManager
{
    protected static string $relationship = 'kegiatan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->options(User::role('panel_user')->get()->pluck('name', 'id')->toArray())
                    ->nullable(),
                Forms\Components\Select::make('periode_id')
                    ->relationship('periode', titleAttribute: 'year')
                    ->searchable()
                    ->preload()
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
                    ->createOptionForm([
                        Section::make('Program Baru')
                            ->schema([
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
                    ])
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('kode')
            ->columns([
                Tables\Columns\TextColumn::make('kode')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_kegiatan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pagu')
                    ->sortable(),
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
