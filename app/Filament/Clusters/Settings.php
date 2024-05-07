<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Navigation\NavigationItem;


class Settings extends Cluster
{
    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make()
                ->label('Postavke')
                ->icon('heroicon-o-squares-2x2')
                ->sort(3)
                ->group('Admin')
                ->url(route('filament.admin.settings.resources.banks.index'))
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(["admin"]);
    }
}
