<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubjectResource\Pages;
use App\Filament\Resources\SubjectResource\RelationManagers;
use App\Models\Subject;
use App\Models\User;
use App\Models\Center;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\RichEditor;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Planes de área';
    protected static ?string $modelLabel = 'Asignatura';
    protected static ?string $pluralLabel = 'Asignaturas';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('grade')
                    ->label('Grado')
                    ->disabled(fn () => !Auth::user()->hasAnyRoleId([
                        User::ROLE_SOPORTE,
                        User::ROLE_DIRECTIVO,
                    ]))
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
                    ]),
                Forms\Components\Select::make('plan_id')
                    ->label('Área')
                    ->relationship('plan', 'name')
                    ->disabled(fn () => !Auth::user()->hasAnyRoleId([
                        User::ROLE_SOPORTE,
                        User::ROLE_DIRECTIVO,
                    ]))
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Asignatura')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('weekly_hours')
                    ->label('Horas semanales')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(1),
                Forms\Components\Select::make('users')
                    ->label('Docentes para esta asignatura')
                    ->relationship('users', 'name')
                ->options(function () {
                        return User::whereHas('roles', fn ($q) => $q->whereIn('id', [User::ROLE_DOCENTE]))
                            ->pluck('name', 'id');
                })
                    ->disabled(fn () => !Auth::user()->hasAnyRoleId([
                        User::ROLE_DIRECTIVO,
                        User::ROLE_SOPORTE,
                    ]))
                    ->multiple()
                    ->preload()
                    ->searchable(),

                Forms\Components\Select::make('interest_centers')
                    ->label('Centros de Interés')
                    ->multiple()
                    ->options(Center::all()->pluck('name', 'name'))
                    ->searchable()
                    ->preload(),

                RichEditor::make('contributions')
                    ->label('Aportes')
                    ->toolbarButtons([
                        'bold', 'italic', 'underline', 'bulletList', 'orderedList'
                    ]),

                RichEditor::make('strategies')
                    ->label('Estrategias')
                    ->toolbarButtons([
                        'bold', 'italic', 'underline', 'bulletList', 'orderedList'
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('grade')
                    ->label('Grado')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan.name')
                    ->label('Área')
                    ->wrap()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Asignatura')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('weekly_hours')
                    ->label('IHS')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('users.name')
                    ->label('Docentes')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->bulleted(),
            ])
            ->groups([
                Group::make('grade')
                ->label('Grado')
                ->collapsible(),
                Group::make('plan.name')
                ->label('Área')
                ->collapsible()
                ->titlePrefixedWithLabel(false),
            ])
            ->defaultGroup('plan.name')
            ->groupingDirectionSettingHidden()
            ->filters([
                Tables\Filters\SelectFilter::make('grade')
                    ->label('Grado')
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
                    ->searchable(),

                Tables\Filters\SelectFilter::make('plan_id')
                    ->label('Área')
                    ->relationship('plan', 'name')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('users')
                    ->label('Docentes')
                    ->multiple()
                    ->relationship('users', 'name')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->icon('heroicon-o-pencil-square')
                    ->color('success')
                    ->tooltip('Editar')
                    ->iconSize('h-6 w-6')
                    ->disabled(function ($record) {
                        $user = Auth::user();
                        return ! $user->hasAnyRoleId([User::ROLE_DIRECTIVO, User::ROLE_SOPORTE]) &&
                               ! $record->users->contains('id', $user->id);
                    }),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\TopicsRelationManager::class,
            RelationManagers\RubricsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
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

        if ($user->hasAnyRoleId([User::ROLE_AREA])) {
            return $query->where(function ($q) use ($user) {
                $q->whereHas('plan.users', fn ($q) => $q->where('users.id', $user->id))
                  ->orWhereHas('users', fn ($q) => $q->where('users.id', $user->id));
            });
        }

        if ($user->hasAnyRoleId([User::ROLE_DOCENTE])) {
            return $query->whereHas('users', fn ($q) => $q->where('users.id', $user->id));
        }

        return $query->whereRaw('0 = 1');
    }
    public static function getNavigationBadge(): ?string
{
    $user = Auth::user();

    if ($user->hasAnyRoleId([
        User::ROLE_SOPORTE,
        User::ROLE_DIRECTIVO,
    ])) {
        return static::getModel()::count();
    }

    if ($user->hasAnyRoleId([
        User::ROLE_DOCENTE
    ]))
    {
        return static::getModel()::whereHas('users', fn ($q) =>
            $q->where('users.id', $user->id)
        )->count();
    }

    if ($user->hasAnyRoleId([
        User::ROLE_AREA
    ])) 
    {
        return static::getModel()::whereHas('plan.users', fn ($q) =>
            $q->where('users.id', $user->id)
        )->count();
    }

    return null;
}
}
