<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    protected static string $view = 'filament.auth.login';

    protected ?string $maxWidth = '7xl';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function hasLogo(): bool
    {
        return false;
    }

    public function getHeading(): string | Htmlable
    {
        return '';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return null;
    }
}
