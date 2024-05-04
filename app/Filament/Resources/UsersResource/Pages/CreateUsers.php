<?php

namespace App\Filament\Resources\UsersResource\Pages;

use App\Filament\Resources\UsersResource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms;
use Illuminate\Support\Facades\DB;
class CreateUsers extends CreateRecord
{
    protected static string $resource = UsersResource::class;

    public function mount(): void
    {
        static::authorizeResourceAccess();
        abort_unless(auth()->user()->hasRole(["admin"]), 403, "Unauthorized action.");
        $this->fillForm();
    }

    public function getTitle(): string
    {
        return "Novi Korisnik";
    }

    public function form(Form $form): Form
    {
        $options = DB::table("roles")
            ->select('name', 'id')
            ->pluck('name', 'id')
            ->toArray();

        return $form->schema([

            TextInput::make('name')
                ->label("Ime i prezime")
                ->required(),
            TextInput::make('email')
                ->label("Email")
                ->unique()
                ->rule("email")
                ->required(),
            TextInput::make('password')
                ->label("Lozinka")
                ->password()
                ->required()
                ->dehydrateStateUsing(fn($state) => bcrypt($state)),
            TextInput::make('passwordConfirmation')
                ->label("Potvrdi lozinku")
                ->password()
                ->same('password')
                ->required(),
            Select::make('role_id')
                ->label("Uloga")
                ->options($options)
                ->required()
        ])
            ->columns(2);
    }


}
