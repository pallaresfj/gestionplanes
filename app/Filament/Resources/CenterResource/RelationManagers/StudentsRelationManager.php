<?php

namespace App\Filament\Resources\CenterResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'students';
    protected static ?string $modelLabel = 'Estudiante';
    protected static ?string $pluralLabel = 'Estudiantes';
    protected static ?string $title = 'Estudiantes';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('full_name')
                ->label('Nombre Completo')
                ->required()
                ->maxLength(150)
                ->columnSpanFull(),
            Forms\Components\TextInput::make('identification')
                ->label('Identificación')
                ->required()
                ->unique(ignorable: fn ($record) => $record)
                ->maxLength(20),
            Forms\Components\Select::make('grade')
                ->label('Curso')
                ->searchable()
                ->required()
                ->options([
                    'Agropecuario' => [
                        'CA 0601' => 'CA 0601',
                        'CA 0602' => 'CA 0602',
                        'CA 0701' => 'CA 0701',
                        'CA 0702' => 'CA 0702',
                        'CA 0801' => 'CA 0801',
                        'CA 0802' => 'CA 0802',
                        'CA 0901' => 'CA 0901',
                        'CA 1001' => 'CA 1001',
                        'CA 1101' => 'CA 1101',
                    ],
                    'Divino Niño' => [
                        'DN 0001'=> 'DN 0001',
                        'DN 0101'=> 'DN 0101',
                        'DN 0201'=> 'DN 0201',
                        'DN 0202'=> 'DN 0202',
                        'DN 0301'=> 'DN 0301',
                        'DN 0302'=> 'DN 0302',
                        'DN 0401'=> 'DN 0401',
                        'DN 0501'=> 'DN 0501',
                    ],
                    'Madre Laura' => [
                        'ML 0001'=> 'ML 0001',
                        'ML 0101'=> 'ML 0101',
                        'ML 0201'=> 'ML 0201',
                        'ML 0301'=> 'ML 0301',
                        'ML 0401'=> 'ML 0401',
                        'ML 0501'=> 'ML 0501',
                        'ML 0502'=> 'ML 0502',
                    ],
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('full_name')
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nombre Completo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('identification')
                    ->label('Identificación')
                    ->searchable(),
                Tables\Columns\TextColumn::make('grade')
                    ->label('Curso')
                    ->searchable(),
            ])
            ->groups([
                Group::make('grade')
                ->label('Curso')
                ->collapsible()
                ->titlePrefixedWithLabel(false),
            ])
            ->defaultGroup('grade')
            ->groupingDirectionSettingHidden()
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->tooltip('Editar')
                    ->iconSize('h-6 w-6'),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->tooltip('Borrar')
                    ->iconSize('h-6 w-6'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->label('Exportar Excel'),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
