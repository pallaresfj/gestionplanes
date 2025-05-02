<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RubricResource\Pages;
use App\Filament\Resources\RubricResource\RelationManagers;
use App\Models\Rubric;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Auth;
use App\Models\Subject;
use Filament\Forms\Components\Textarea;
use App\Models\User;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class RubricResource extends Resource
{
    protected static ?string $model = Rubric::class;
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = 'Planes de área';
    protected static ?string $modelLabel = 'Rúbrica';
    protected static ?string $pluralLabel = 'Rúbricas';

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    public static function form(Form $form): Form
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
                    ->required(),
                Select::make('subject_id')
                    ->label('Asignatura')
                    ->options(function () {
                        $user = Auth::user();

                        if ($user->hasAnyRoleId([
                            User::ROLE_DIRECTIVO,
                            User::ROLE_SOPORTE,
                        ])) {
                            return Subject::pluck('name', 'id');
                        }

                        if ($user->hasAnyRoleId([
                            User::ROLE_AREA
                        ])) 
                        {
                            $planIds = $user->plans()->pluck('plans.id');
                            return Subject::whereIn('plan_id', $planIds)->pluck('name', 'id');
                        }

                        if ($user->hasAnyRoleId([
                            User::ROLE_DOCENTE
                        ])) 
                        {
                            return $user->subjects()->select('subjects.name', 'subjects.id')->pluck('subjects.name', 'subjects.id');
                        }

                        return [];
                    })
                    ->searchable()
                    ->preload()
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

    public static function table(Table $table): Table
    {
        return $table
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
                ->collapsible(),
                Group::make('subject.name')
                ->label('Asignatura')
                ->collapsible()
                ->titlePrefixedWithLabel(false),
            ])
            ->defaultGroup('period')
            ->groupingDirectionSettingHidden()
            ->filters([
                Tables\Filters\SelectFilter::make('period')
                    ->label('Periodo')
                    ->options([
                        '1' => 'Primero',
                        '2' => 'Segundo',
                        '3' => 'Tercero',
                    ])
                    ->searchable(),

                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Asignatura')
                    ->relationship('subject', 'name')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('subject.plan_id')
                    ->label('Área')
                    ->relationship('subject.plan', 'name')
                    ->searchable(),
            ])
            ->persistFiltersInSession()
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRubrics::route('/'),
            'create' => Pages\CreateRubric::route('/create'),
            'edit' => Pages\EditRubric::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if ($user->hasAnyRoleId([
            User::ROLE_DIRECTIVO,
            User::ROLE_SOPORTE,
        ])) {
            return $query;
        }

        if ($user->hasAnyRoleId([
            User::ROLE_DOCENTE
        ])) 
        {
            return $query->whereHas('subject.users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        if ($user->hasAnyRoleId([
            User::ROLE_AREA
        ])) 
        {
            return $query->whereHas('subject.plan.users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        return $query->whereRaw('0 = 1');
    }
}