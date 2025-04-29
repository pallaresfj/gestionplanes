<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\Center;
use App\Models\Plan;
use App\Models\Subject;

class StatsOverview extends BaseWidget
{


    protected function getStats(): array
    {
        return [
            Stat::make('Centros de interés', Center::all()->count())
                ->description('Total centros de interés creados y ejecuntandose')
                ->color('success'),
            Stat::make('Áreas', Plan::all()->count())
                ->description('Áreas contempladas en el plan de estudios')
                ->color('success'),
            Stat::make('Asignaturas', Subject::all()->count())
                ->description('Asignaturas gestionadas en la plataforma')
                ->color('success'),
        ];
    }
}
