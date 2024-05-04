<?php

namespace App\Filament\Resources\UsersResource\Pages;

use App\Filament\Resources\UsersResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class EditUsers extends EditRecord
{
    protected static string $resource = UsersResource::class;

    // public $record;

    public function mount($record): void
    {
        parent::mount($record);
        abort_unless(auth()->user()->hasRole(["admin"]), 403, "Unauthorized action.");
    }

    public function getTitle(): string
    {
        return "Uredi Korisnika";
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
                ->disabled(),
            Select::make('role_id')
                ->label("Uloga")
                ->options($options)
                ->required(),
        ])
            ->columns(2);
    }

    protected function afterSave(): void
    {
        $role = Role::findById($this->data['role_id']);
        $this->record->syncRoles($role);
    }

    protected function getActions(): array
    {
        $actions = parent::getActions();
        $actions[] = Action::make('password')
            ->label("Nova lozinka")
            ->action(fn() => $this->openPasswordModal())
            ->form([
                TextInput::make('password')
                    ->label("Nova lozinka")
                    ->password()
                    ->required(),
                TextInput::make('passwordConfirmation')
                    ->label("Potvrdi lozinku")
                    ->password()
                    ->same('password')
                    ->required(),
            ]);
        return $actions;
    }

    public function openPasswordModal()
    {
        $this->record->password = bcrypt($this->mountedActionData['password']);
        $this->record->save();
    }

    public function removeUser()
    {
        $this->record->delete();
        $this->redirectRoute('filament.admin.resources.users.index');
    }


}
