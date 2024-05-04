<?php

namespace App\Filament\Clusters\Settings\Resources\PaymentItemsResource\Pages;

use App\Filament\Clusters\Settings\Resources\PaymentItemsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentItems extends ListRecords
{
    protected static string $resource = PaymentItemsResource::class;

    public function mount(): void
    {
        static::authorizeResourceAccess();
        abort_unless(auth()->user()->hasRole(["admin"]), 403, "Unauthorized action.");
    }
    public function getTitle(): string
    {
        return "Razlozi plačanja";
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
