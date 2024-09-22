<?php

namespace App\Filament\Resources\MembersResource\Pages;

use App\Filament\Resources\MembersResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

use App\Models\Residences;

class CreateMembers extends CreateRecord
{
    protected static string $resource = MembersResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            Card::make()
                ->schema([
                    TextInput::make('first_name')
                        ->label('Ime')
                        ->required(),
                    TextInput::make('last_name')
                        ->label('Prezime')
                        ->required(),
                    TextInput::make('id_number')
                        ->label('Lični broj'),
                    Select::make('residence_id')
                        ->label('Prebivalište')
                        ->options(Residences::pluck('name', 'id'))
                        ->required(),
                    // TextInput::make('no_family_members')
                    //     ->label('Broj članova porodice')
                    //     ->numeric()
                    //     ->required(),
                ])
                ->columnSpan([
                    'default' => 1
                ]),
            Card::make()
                ->schema([
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->rule('email'),
                    TextInput::make('tel')
                        ->label('Broj telefona')
                        ->tel(),
                ])
                ->columnSpan([
                    'default' => 1
                ]),
        ]);
    }

    public function getTitle(): string
    {
        return 'Novi Član';
    }

    
}
