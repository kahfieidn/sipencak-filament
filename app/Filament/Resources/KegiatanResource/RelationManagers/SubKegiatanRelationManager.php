<?php

namespace App\Filament\Resources\KegiatanResource\RelationManagers;

use App\Models\SubKegiatan;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;


use Filament\Tables\Columns\Summarizers\Count;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class SubKegiatanRelationManager extends RelationManager
{
    protected static string $relationship = 'sub_kegiatan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama_sub_kegiatan')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('kode')
            ->columns([
                Tables\Columns\TextColumn::make('kode'),
                Tables\Columns\TextColumn::make('nama_sub_kegiatan'),
                Tables\Columns\TextColumn::make('pagu')
                    ->label('Pagu')
                    ->summarize(
                        Sum::make('pagu')
                    ),
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
                Tables\Actions\Action::make('Input Biaya')
                    ->fillForm(fn (SubKegiatan $record): array => [
                        'id' => $record->id,
                        'pagu' => $record->pagu, // Ensure the pagu field is included in the form fill
                    ])
                    ->form([
                        Select::make('id')
                            ->label('ID User')
                            ->options(SubKegiatan::query()->pluck('kode', 'id'))
                            ->required(),
                        Forms\Components\TextInput::make('pagu') // Ensure this field is required if needed
                            ->label('Pagu') // Add a label for clarity
                            ->required(), // Add validation if necessary
                    ])
                    ->action(function (array $data, SubKegiatan $record): void {
                        $record->id = $data['id']; // Set the ID directly
                        $record->pagu = $data['pagu']; // Set the pagu attribute directly
                        $record->save(); // Save the record
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
