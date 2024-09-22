<?php

namespace App\Filament\Clusters\Settings\Resources\ResidencesResource\Pages;

use App\Filament\Clusters\Settings\Resources\ResidencesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListResidences extends ListRecords
{
    protected static string $resource = ResidencesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
