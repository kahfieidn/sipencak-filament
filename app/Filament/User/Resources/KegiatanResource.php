<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\KegiatanResource\Pages;
use App\Filament\User\Resources\KegiatanResource\RelationManagers;
use App\Models\Kegiatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KegiatanResource extends Resource
{
    protected static ?string $model = Kegiatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('program_id')
                    ->relationship('program', 'id')
                    ->required(),
                Forms\Components\Select::make('periode_id')
                    ->relationship('periode', 'id')
                    ->required(),
                Forms\Components\TextInput::make('kode')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama_kegiatan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('pagu')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('status')
                    ->required(),
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
            ])->defaultGroup('program.nama_program')->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'create' => Pages\CreateKegiatan::route('/create'),
            'view' => Pages\ViewKegiatan::route('/{record}'),
            'edit' => Pages\EditKegiatan::route('/{record}/edit'),
        ];
    }
}
