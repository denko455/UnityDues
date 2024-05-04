<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentsResource\Pages;
use App\Models\Payments;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Navigation\NavigationItem;


use App\Models\PaymentItems;
use App\Models\Banks;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;



class PaymentsResource extends Resource
{
    protected static ?string $model = Payments::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';   

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make()
                ->label('Plaćanja')
                ->icon('heroicon-o-banknotes')
                ->sort(2)
                ->url(route('filament.admin.resources.payments.index'))
        ];
    }
    
    public static function form(Form $form): Form
    {
        $memberItems = new PaymentItems();
        $items = $memberItems->getMembersPaymentItemsTextList();

        $baks = Banks::getBanks();

        return $form
            ->schema([
                Grid::make([
                    'default' => 2
                ])
                    ->schema([
                        TextInput::make('document_number')
                            ->label('Broj dokumenta')
                            ->disabled(function (Get $get) {
                                return $get('status') == 'approved';
                            }),
                        DatePicker::make('document_date')
                            ->label('Datum documenta')
                            ->native(false)
                            ->displayFormat('d.m.Y')
                            ->maxDate(now())
                            ->default(now())
                            ->required(),
                        Select::make('payment_item_id')
                            ->label('Razlog plaćanja')
                            ->options($items)
                            ->required(),
                        TextInput::make('year')
                            ->label('Godina')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(9999)
                            ->reactive()
                            ->afterStateHydrated(function (TextInput $component, $state) {
                                $date = null;
                                if(!isset($state)) {
                                    $date = date('Y');
                                } else {
                                    $date = date('Y', strtotime($state));
                                }
                                $component->state($date);
                            })
                            ->dehydrateStateUsing(fn ($state) => $state.'-01-01'),
                    ])
                    ->columnSpan(1),
                TextInput::make('value')
                    ->label('Vrijedonst')
                    ->default(0.00)
                    ->numeric(true)
                    ->minValue(0.01)
                    ->suffix('EUR')
                    ->required(),
                Select::make('bank_id')
                    ->label('Banka')
                    ->options($baks)
                    ->required(),
                Textarea::make('remarks')
                    ->label('Bilješke')
            ]);
    }
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/')
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(["admin"]);
    }
}