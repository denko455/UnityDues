<?php

namespace App\Filament\Resources\ProjectsResource\Pages;

use App\Filament\Resources\ProjectsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\CheckboxColumn;


class ListProjects extends ListRecords
{
    protected static string $resource = ProjectsResource::class;

    public function mount(): void
    {
        static::authorizeResourceAccess();
        abort_unless(auth()->user()->hasRole(["admin"]), 403, "Unauthorized action.");
    }
    public function getTitle(): string
    {
        return "Projekti";
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make("name")->label("Ime projekta"),
            TextColumn::make("description")->label("Opis")->wrap(),
            CheckboxColumn::make("is_active")->label("Aktivan"),
        ]);
    }
}
