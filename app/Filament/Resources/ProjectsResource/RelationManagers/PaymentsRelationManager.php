<?php

namespace App\Filament\Resources\ProjectsResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Query\Builder;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\IconColumn;


class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('document_number')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document_number')
                    ->label('Broj dokumenta')
                    ->icon(function (Model $record) {
                        if ($record->payment_item->type === 'in') {
                            return 'heroicon-o-arrow-trending-up';
                        }
                        return 'heroicon-o-arrow-trending-down';
                    })
                    ->iconColor(function (Model $record) {
                        if ($record->payment_item->type === 'in') {
                            return 'success';
                        }
                        return 'danger';
                    })
                    ->description(fn (Model $record): string => date('d.m.Y', strtotime($record->document_date))),
                TextColumn::make('payment_item.name')
                    ->label('Svrha plaćanja'),                    
                TextColumn::make('bank.name')
                    ->label('Banka'),
                TextColumn::make('value')
                    ->label('Vrijedonst')
                    ->money(function(Model $record){
                        return $record->currency;
                    }, true)
                    ->summarize(                        
                        Summarizer::make()
                            ->label('Ukupno ')
                            ->using(function(Builder $query){
                                $query1In = clone $query;
                                $query2In = clone $query;
                                $query3In = clone $query;
                                $query1Out = clone $query;
                                $query2Out = clone $query;
                                $query3Out = clone $query;
                                
                                $resEurIn = $query1In->join('payment_items', 'payments.payment_item_id', '=', 'payment_items.id')->where('payment_items.type', 'in')->where('currency', 'EUR')->sum('value'); // Get all the bindings
                                $resChfIn = $query2In->join('payment_items', 'payments.payment_item_id', '=', 'payment_items.id')->where('payment_items.type', 'in')->where('currency', 'CHF')->sum('value'); // Get all the bindings
                                $resUsdIn = $query3In->join('payment_items', 'payments.payment_item_id', '=', 'payment_items.id')->where('payment_items.type', 'in')->where('currency', 'USD')->sum('value'); // Get all the bindings
                                $resEurOut = $query1Out->join('payment_items', 'payments.payment_item_id', '=', 'payment_items.id')->where('payment_items.type', 'out')->where('currency', 'EUR')->sum('value'); // Get all the bindings
                                $resChfOut = $query2Out->join('payment_items', 'payments.payment_item_id', '=', 'payment_items.id')->where('payment_items.type', 'out')->where('currency', 'CHF')->sum('value'); // Get all the bindings
                                $resUsdOut = $query3Out->join('payment_items', 'payments.payment_item_id', '=', 'payment_items.id')->where('payment_items.type', 'out')->where('currency', 'USD')->sum('value'); // Get all the bindings
                                // dd($resEur);
                                $srt = [];
                                $oFormatter = new \NumberFormatter('de_DE', \NumberFormatter::CURRENCY);
        
                                if($resEurIn - $resEurOut) $srt[] = $oFormatter->formatCurrency($resEurIn - $resEurOut, 'EUR');
                                if($resChfIn - $resChfOut) $srt[] = $oFormatter->formatCurrency($resChfIn - $resChfOut, 'CHF');
                                if($resUsdIn - $resUsdOut) $srt[] = $oFormatter->formatCurrency($resUsdIn - $resUsdOut, 'USD');
                                return implode(', ', $srt);
                            })
                        )
                    ->alignRight(),
                IconColumn::make('remarks')
                    ->label('Bilješke')
                    ->icon(function ($state) {
                        return $state ? 'heroicon-o-envelope' : '';
                    })
                    ->visible(fn($state) => empty ($state))
                    ->alignCenter(true)
                    ->action(
                        Tables\Actions\Action::make('message')
                            ->modalHeading('Bilješka')
                            ->modalSubmitAction(false)
                            ->requiresConfirmation()
                            ->modalIcon(null)
                            ->modalDescription(function (Model $record) {
                                return $record->remarks;
                            })
                    ),               
                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(function($state){
                        $statuses = [
                            'draft' => 'Nacrt',
                            'approved' => 'Odobreno',
                        ];
                        return $statuses[$state];
                    })
                    ->badge()
                    ->grow(false)
                    ->alignCenter()
                    ->color(function($state){
                        if($state === 'draft') return 'gray';
                        if($state === 'approved') return 'success';
                    })
            ])
            ->defaultSort('document_date', 'desc')
            ->heading('Plaćanja');
    }
}
