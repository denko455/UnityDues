<?php

namespace App\Filament\Resources\MembersResource\Pages;

use App\Filament\Resources\MembersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Form;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Pages\Actions\Action;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class EditMembers extends EditRecord
{
    protected static string $resource = MembersResource::class;

    protected function getHeaderActions(): array
    {
        if(auth()->user()->hasRole(["admin"])) {
            return [
                Actions\DeleteAction::make(),
            ];
        }
        return [];
    }

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
                    // TextInput::make('no_family_members')
                    //     ->label('Broj članova porodice')
                    //     ->numeric()
                    //     ->required(),
                ])                 
                ->disabled(fn (Model $record)=> !auth()->user()->hasRole(["admin"]))
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
        return 'Uredi člana';
    }

    public function getActions(): array
    {
        return [
            Action::make('download-csv1')
                ->label('PDF')
                ->tooltip('Download')
                ->icon('heroicon-s-document-download')
                ->form([
                   Select::make('lang')
                        ->label(__('filament::additional.global.language'))
                        ->options(getAvailableLanguages())
                        ->required()
                ])
                ->action(function () {
                    if(isset($this->mountedActionData['lang'])){
                        $route = route('pdf.member-profile-pdf', [
                            'id' => $this->record->id,
                            'lang' => $this->mountedActionData['lang']
                        ]);
                        $this->redirect($route);
                    }
                    return null;
                }),
        ];
    }
}
