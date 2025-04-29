<?php

namespace App\Filament\AvatarProviders;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Database\Eloquent\Model;

class CustomAvatarProvider implements AvatarProvider
{
    public function get(Model $record): string
    {
        // Si el modelo tiene el método 'getFilamentAvatarUrl', úsalo
        if (method_exists($record, 'getFilamentAvatarUrl') && $record->getFilamentAvatarUrl()) {
            return $record->getFilamentAvatarUrl();
        }

        // Retorna un avatar por defecto si no hay foto de perfil
        return 'https://ui-avatars.com/api/?name=' . urlencode($record->name);
    }
}