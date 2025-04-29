<?php

namespace App\Filament\Resources\CenterResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\Summarizers\Sum;

class BudgetsRelationManager extends RelationManager
{
    protected static string $relationship = 'budgets';
    protected static ?string $modelLabel = 'Recurso';
    protected static ?string $pluralLabel = 'Recursos';
    protected static ?string $title = 'Recursos';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Grid::make(3)->schema([
                Forms\Components\TextInput::make('quantity')
                    ->label('Cantidad')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(1),
                Forms\Components\TextInput::make('item')
                    ->label('Item')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('unit_value')
                    ->label('Valor Unitario')
                    ->default(0.00),
            ]),
            Forms\Components\Textarea::make('observations')
                ->label('Observaciones')
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('item')
            ->columns([
                TextInputColumn::make('quantity')
                    ->label('Cantidad')
                    ->rules(['numeric', 'min:1']),
                TextInputColumn::make('item')
                    ->rules(['required', 'max:100'])
                    ->label('Item'),
                TextInputColumn::make('unit_value')
                    ->label('Valor Unitario')
                    ->rules(['numeric', 'min:1']),
                Tables\Columns\TextColumn::make('total_value')
                    ->label('Total')
                    ->summarize(
                        Sum::make()
                            ->label('')
                            ->formatStateUsing(fn ($state) =>
                                '<span class="font-bold text-lg text-gray-800">$' . number_format($state, 2, ',', '.') . '</span>'
                            )
                            ->html()
                    )
                    ->money('COP', locale: 'es_CO'),
                Tables\Columns\TextColumn::make('observations')
                    ->label('Observaciones')
                    ->lineClamp(1)
                    ->wrap(),
            ])
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
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->icon('heroicon-o-eye')
                    ->color('secondary')
                    ->tooltip('Ver')
                    ->iconSize('h-6 w-6')
                    ->modalHeading(fn ($record) => $record->item),
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
