<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentsResource\Pages;
use App\Models\Payments;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Navigation\NavigationItem;
use Filament\Forms\Components\Fieldset;

use App\Models\PaymentItems;
use App\Models\Banks;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;

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
                ->group('Admin')
                ->sort(0)
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
                    Fieldset::make('')
                    ->schema([
                        TextInput::make('document_number')
                            ->label('Broj dokumenta')
                            ->required(),
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
                        
                        Textarea::make('remarks')
                            ->label('Bilješke')
                    ]),
                    Fieldset::make('')
                    ->schema([
                        TextInput::make('value')
                            ->label('Vrijedonst')
                            ->default(0.00)
                            ->numeric(true)
                            ->minValue(0.01)
                            ->suffix(fn (Get $get)=>$get('currency'))
                            ->required(),
                        Select::make('currency')
                            ->label('Valuta')
                            ->default('EUR')
                            ->options([
                                'EUR'=>'Euro (EUR)',
                                'CHF'=>'Švicarska Franka (CHF)',
                                'USD'=>'Američki dolar (USD)',                                
                            ])
                            ->live()                
                            ->required(),
                        Select::make('bank_id')
                            ->label('Banka')
                            ->options($baks)
                            ->required(),
                    ]),
                ])                
            ]);
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

    public static function getGloballySearchableAttributes(): array
    {
        if(auth()->user()->hasRole(["admin"])) {
            return ['document_number'];
        }
        return [];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        if(auth()->user()->hasRole(["admin"])) {
            $fmt = numfmt_create( 'de_DE', \NumberFormatter::CURRENCY );
            return [
                'Datum dokumenta' => date('d.m.Y', strtotime($record->document_date)) ?? '-',
                'Ukupno' => numfmt_format_currency($fmt,  $record->total ?? 0, $record->currency),
            ];
        }
        return [];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->document_number;
    }
}
