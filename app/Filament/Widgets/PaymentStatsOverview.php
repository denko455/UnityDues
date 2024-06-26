<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\Payments;
class PaymentStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $aStates = [];
        if(auth()->user()->hasRole(['admin'])) {
            $aStates[] = Stat::make('Ukupna ne odobrena plačanja', Payments::getCount('draft'))
                        ->description(Payments::getValueSum('draft'))
                        ->color('success');
            $aStates[] = Stat::make('Ukupna odobrena plačanja', Payments::getCount('approved'))
                        ->description(Payments::getValueSum('approved'))
                        ->color('success');
        } else {
            $aStates[] = Stat::make('Tvoja ne odobrena plačanja', Payments::getCount('draft', auth()->user()->id))
                        ->description(Payments::getValueSum('draft', auth()->user()->id))
                        ->color('success');
            $aStates[] = Stat::make('Tvoja odobrena plačanja', Payments::getCount('approved', auth()->user()->id))
                        ->description(Payments::getValueSum('approved', auth()->user()->id))
                        ->color('success');
        }

        return $aStates;
    }
}
