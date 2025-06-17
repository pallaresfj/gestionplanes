<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CenterResource\Pages;
use App\Filament\Resources\CenterResource\RelationManagers;
use App\Models\Center;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class CenterResource extends Resource
{
    protected static ?string $model = Center::class;
    protected static ?string $modelLabel = 'Centro';
    protected static ?string $pluralLabel = 'Centros';
    protected static ?string $navigationGroup = 'Centros de interés';
    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Plan de Área')
                    ->tabs([
                        Tab::make('Identificación')->schema([
    
                            Forms\Components\FileUpload::make('image_path')
                                ->label('Portada')
                                ->image()
                                ->imageEditor()
                                ->directory('center-cover')
                                ->visibility('public')
                                ->columnSpanFull(),
    
                                Grid::make(2)->schema([
                                    Forms\Components\Select::make('user_id')
                                        ->label('Responsable')
                                        ->options(
                                            User::whereHas('roles', fn ($q) => $q->whereIn('id', [
                                                User::ROLE_CENTRO,
                                            ]))
                                            ->orderBy('name')
                                            ->pluck('name', 'id')
                                        )
                                        ->searchable()
                                        ->disabled(fn () => !Auth::user()->hasAnyRoleId([
                                            User::ROLE_SOPORTE,
                                            User::ROLE_DIRECTIVO,
                                        ]))
                                        ->required(),
                                    Forms\Components\TextInput::make('academic_year')
                                        ->label('Año')
                                        ->required()
                                        ->maxLength(4),
                                ]),
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre')
                                    ->placeholder('Escriba el nombre del centro de ineterés')
                                    ->required()
                                    ->maxLength(100)
                                    ->columnSpanFull(),
                        ]),
    
                        Tab::make('Descripción')->schema([
                            RichEditor::make('description')
                                ->label('')
                                ->disableToolbarButtons([
                                    'attachFiles',
                                ])
                                ->columnSpanFull(),
                        ]),
    
                        Tab::make('Objetivo')->schema([
                            Forms\Components\Textarea::make('objective')
                                ->label('')
                                ->columnSpanFull(),
                        ]),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('academic_year')
                    ->label('Año')    
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Centro de Interés')    
                    ->searchable()
                    ->wrap()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Equipo Responsable')    
                    ->numeric()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Portada')
                    ->defaultImageUrl(url('/images/portada.jpg'))
                    ->disk('public')
                    ->width(100)
                    ->height(59),
            ])
            ->defaultSort('name', 'asc')
            ->filters([
                //
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
            RelationManagers\TeachersRelationManager::class,
            RelationManagers\StudentsRelationManager::class,
            RelationManagers\ActivitiesRelationManager::class,
            RelationManagers\BudgetsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCenters::route('/'),
            'create' => Pages\CreateCenter::route('/create'),
            'edit' => Pages\EditCenter::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if ($user->hasAnyRoleId([
            User::ROLE_DIRECTIVO,
            User::ROLE_SOPORTE,
        ]))
        {
            return $query;
        }

        if ($user->hasAnyRoleId([
            User::ROLE_CENTRO
        ]))
        {
            return $query->where('user_id', $user->id);
        }

        return $query->whereRaw('0 = 1');
    }
    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();

        if ($user->hasAnyRoleId([
            User::ROLE_DIRECTIVO,
            User::ROLE_SOPORTE,
        ])) {
            return static::getModel()::count();
        }

        if ($user->hasAnyRoleId([
            User::ROLE_CENTRO
        ]))
        {
            return static::getModel()::where('user_id', $user->id)->count();
        }

        return null;
    }
}
