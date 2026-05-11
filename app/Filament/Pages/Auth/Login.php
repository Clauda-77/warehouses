<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
 
use Filament\Forms\Components\Select;
use Filament\Forms\Form;


class Login extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.login';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                 Select::make('role')
                    ->label('اختر الدور')
                    ->options([
                        'admin' => 'مدير النظام',
                        'manager' => 'مدير',
                        'warehouse_keeper' => 'أمين مخزن',
                    ])
                    ->required(),
                 $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'], // نمرر الدور إلى credentials
        ];
    }
}