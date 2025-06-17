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

class TopicsRelationManager extends RelationManager
{
    protected static string $relationship = 'topics';
    protected static ?string $modelLabel = 'Contenido';
    protected static ?string $pluralLabel = 'Contenidos';
    protected static ?string $title = 'Contenidos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('period')
                    ->label('Periodo')
                    ->native(false)
                    ->placeholder('Seleccione un periodo')
                    ->options([
                        '1' => 'Primero',
                        '2' => 'Segundo',
                        '3' => 'Tercero',
                    ])
                    ->columnSpanFull()
                    ->required(),
                
                Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\RichEditor::make('standard')
                    ->label('Estándar')
                    ->disableToolbarButtons([
                        'attachFiles',
                        'blockquote',
                        'strike',
                        'codeBlock',
                        'link',
                    ]),
                    Forms\Components\RichEditor::make('dba')
                    ->label('DBA')
                    ->disableToolbarButtons([
                        'attachFiles',
                        'blockquote',
                        'strike',
                        'codeBlock',
                        'link',
                    ]),
                Forms\Components\RichEditor::make('competencies')
                    ->label('Competencias')
                    ->disableToolbarButtons([
                        'attachFiles',
                        'blockquote',
                        'strike',
                        'codeBlock',
                        'link',
                    ]),
                Forms\Components\RichEditor::make('contents')
                    ->label('Contenidos')
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
            ->recordTitleAttribute('period')
            ->columns([
                Tables\Columns\TextColumn::make('period')
                    ->label('Periodo')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('standard')
                    ->label('Estándar')
                    ->html()
                    ->wrap()
                    ->lineClamp(2),
                Tables\Columns\TextColumn::make('dba')
                    ->label('DBA')
                    ->html()
                    ->wrap()
                    ->lineClamp(2),
                Tables\Columns\TextColumn::make('competencies')
                    ->label('Competencias')
                    ->html()
                    ->wrap()
                    ->lineClamp(2),
                Tables\Columns\TextColumn::make('contents')
                    ->label('Contenidos')
                    ->html()
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
