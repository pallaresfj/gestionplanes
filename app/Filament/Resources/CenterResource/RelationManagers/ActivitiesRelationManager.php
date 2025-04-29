<?php

namespace App\Filament\Resources\CenterResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';
    protected static ?string $modelLabel = 'Actividad';
    protected static ?string $pluralLabel = 'Actividades';
    protected static ?string $title = 'Actividades';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\DatePicker::make('week')
                ->label('Semana')
                ->required(),
            Forms\Components\Textarea::make('activity')
                ->label('Actividad')
                ->required(),
            Forms\Components\Textarea::make('objective')
                ->label('Objetivo de la Actividad')
                ->columnSpanFull(),
            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\RichEditor::make('methodology')
                        ->label('Metodología')
                        ->disableToolbarButtons([
                            'attachFiles',
                            'blockquote',
                            'strike',
                            'codeBlock',
                            'link',
                        ]),
                    Forms\Components\RichEditor::make('materials')
                        ->label('Materiales')
                        ->disableToolbarButtons([
                            'attachFiles',
                            'blockquote',
                            'strike',
                            'codeBlock',
                            'link',
                        ]),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('activity')
            ->columns([
                Tables\Columns\TextColumn::make('week')
                    ->label('Semana')
                    ->wrap()
                    ->date('F j \d\e Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('activity')
                    ->label('Actividad')
                    ->wrap(),
                Tables\Columns\TextColumn::make('objective')
                    ->label('Objetivo')
                    ->lineClamp(4)
                    ->wrap(),
                Tables\Columns\TextColumn::make('methodology')
                    ->label('Metodología')
                    ->html()
                    ->lineClamp(4)
                    ->wrap(),
                Tables\Columns\TextColumn::make('materials')
                    ->label('Materiales')
                    ->html()
                    ->lineClamp(4)
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
                    ->modalHeading(fn ($record) => $record->activity . ' (' . $record->week?->translatedFormat('F j \d\e Y') . ')'),
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
