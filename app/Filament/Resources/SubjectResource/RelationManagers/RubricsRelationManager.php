<?php

namespace App\Filament\Resources\SubjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class RubricsRelationManager extends RelationManager
{
    protected static string $relationship = 'rubrics';
    protected static ?string $modelLabel = 'Rúbrica';
    protected static ?string $pluralLabel = 'Rúbricas';
    protected static ?string $title = 'Rúbricas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('period')
                    ->label('Periodo')
                    ->options([
                        '1' => 'Primero',
                        '2' => 'Segundo',
                        '3' => 'Tercero',
                    ])
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\Textarea::make('criterion')
                    ->label('Criterio')
                    ->rows(2)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('superior_level')
                    ->label('Superior')
                    ->rows(3)
                    ->maxLength(255),
                Forms\Components\Textarea::make('high_level')
                    ->label('Alto')
                    ->rows(3)
                    ->maxLength(255),
                Forms\Components\Textarea::make('basic_level')
                    ->label('Básico')
                    ->rows(3)
                    ->maxLength(255),
                Forms\Components\Textarea::make('low_level')
                    ->label('Bajo')
                    ->rows(3)
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('period')
            ->columns([
                Tables\Columns\TextColumn::make('period')
                    ->label('Periodo')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Asignatura')
                    ->wrap()
                    ->sortable(),
                Tables\Columns\TextColumn::make('criterion')
                    ->label('Criterio')
                    ->wrap()
                    ->lineClamp(2),
                Tables\Columns\TextColumn::make('superior_level')
                    ->label('Superior')
                    ->wrap()
                    ->lineClamp(2),
                Tables\Columns\TextColumn::make('high_level')
                    ->label('Alto')
                    ->wrap()
                    ->lineClamp(2),
                Tables\Columns\TextColumn::make('basic_level')
                    ->label('Básico')
                    ->wrap()
                    ->lineClamp(2),
                Tables\Columns\TextColumn::make('low_level')
                    ->label('Bajo')
                    ->wrap()
                    ->lineClamp(2),
            ])
            ->groups([
                Group::make('period')
                ->label('Periodo')
                ->collapsible()
                ->titlePrefixedWithLabel(false),
            ])
            ->defaultGroup('period')
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
                    ->color('success')
                    ->tooltip('Editar')
                    ->iconSize('h-6 w-6'),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->tooltip('Borrar')
                    ->iconSize('h-6 w-6'),
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->icon('heroicon-o-eye')
                    ->color('secondary')
                    ->tooltip('Ver')
                    ->iconSize('h-6 w-6')
                    ->modalHeading(fn ($record) => 'Periodo ' . $record->period . ': ' . $record->subject->name),
                Tables\Actions\ReplicateAction::make()
                    ->label('')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->tooltip('Duplicar')
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
