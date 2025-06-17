<?php

namespace App\Filament\Resources\PlanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

use App\Filament\Resources\SubjectResource;
use Filament\Tables\Actions\Action;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Models\User;

class SubjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'subjects';
    protected static ?string $modelLabel = 'Asignatura';
    protected static ?string $pluralLabel = 'Asignaturas';
    protected static ?string $title = 'Asignaturas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(12)->schema([
                    Forms\Components\Select::make('grade')
                        ->label('Grado')
                        ->native(false)
                        ->placeholder('Seleccione un grado')
                        ->required()
                        ->options([
                            '0' => 'Transición',
                            '1' => 'Primero',
                            '2' => 'Segundo',
                            '3' => 'Tercero',
                            '4' => 'Cuarto',
                            '5' => 'Quinto',
                            '6' => 'Sexto',
                            '7' => 'Séptimo',
                            '8' => 'Octavo',
                            '9' => 'Noveno',
                            '10' => 'Décimo',
                            '11' => 'Undécimo',
                        ])
                        ->visible(fn () => !Auth::user()->hasRole('Centro'))
                        ->columnSpan(4),
                    Forms\Components\TextInput::make('name')
                        ->label('Asignatura')
                        ->required()
                        ->maxLength(100)
                        ->columnSpan(6),
                    Forms\Components\TextInput::make('weekly_hours')
                        ->label('IHS')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->columnSpan(2),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();

                if ($user->hasAnyRoleId([
                    \App\Models\User::ROLE_DIRECTIVO,
                    \App\Models\User::ROLE_SOPORTE,
                ])) {
                    return $query;
                }

                if ($user->hasAnyRoleId([
                    \App\Models\User::ROLE_AREA,
                    \App\Models\User::ROLE_DOCENTE,
                ]))
                {
                    return $query->whereHas('users', fn ($q) => $q->where('users.id', $user->id));
                }

                return $query->whereRaw('0 = 1');
            })
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('grade')
                    ->label('Grado')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Asignatura')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('weekly_hours')
                    ->label('IHS')
                    ->numeric()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('users.name')
                    ->label('Docentes')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->bulleted(),
            ])
            ->defaultSort('grade', 'asc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Action::make('abrir')
                    ->label('')
                    ->color('success')
                    ->tooltip('Editar')
                    ->iconSize('h-6 w-6')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn ($record) => SubjectResource::getUrl('edit', ['record' => $record])),
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
                    ->modalHeading(fn ($record) => $record->plan->name . ': ' . $record->name),
                Tables\Actions\ReplicateAction::make()
                    ->label('')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->tooltip('Duplicar')
                    ->iconSize('h-6 w-6')
                    ->visible(fn () => Auth::user()->hasAnyRoleId([User::ROLE_SOPORTE, User::ROLE_DIRECTIVO])),
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
