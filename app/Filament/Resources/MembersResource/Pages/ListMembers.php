<?php

namespace App\Filament\Resources\MembersResource\Pages;

use App\Filament\Resources\MembersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ListMembers extends ListRecords
{
    protected static string $resource = MembersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return __('Članovi');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_number')
                    ->label('Lični broj')
                    ->default("-"),
                TextColumn::make('full_name')->label('Ime i Prezime'),
                TextColumn::make('email')
                    ->label('Email')
                    ->default("-"),
                TextColumn::make('tel')
                    ->label("tel.")
                    ->default("-"),
                TextColumn::make('no_family_members')->label('Broj članova porodice'),
            ]);
    }
    
}
