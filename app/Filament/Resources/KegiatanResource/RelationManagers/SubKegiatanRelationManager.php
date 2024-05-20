<?php

namespace App\Filament\Resources\KegiatanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubKegiatanRelationManager extends RelationManager
{
    protected static string $relationship = 'sub_kegiatan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kegiatan_id')
                    ->relationship('kegiatan', 'nama_kegiatan')
                    ->searchable()
                    ->native(false)
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('kode')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama_sub_kegiatan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('pagu')
                    ->required()
                    ->numeric()
                    ->default(0.00),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('kode')
            ->columns([
                Tables\Columns\TextColumn::make('kode'),
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
