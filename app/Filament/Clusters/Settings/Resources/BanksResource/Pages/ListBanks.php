<?php

namespace App\Filament\Clusters\Settings\Resources\BanksResource\Pages;

use App\Filament\Clusters\Settings\Resources\BanksResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBanks extends ListRecords
{
    protected static string $resource = BanksResource::class;

    public function mount(): void
    {
        static::authorizeResourceAccess();
        abort_unless(auth()->user()->hasRole(["admin"]), 403, "Unauthorized action.");
    }
    public function getTitle(): string
    {
        return "Banka";
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
