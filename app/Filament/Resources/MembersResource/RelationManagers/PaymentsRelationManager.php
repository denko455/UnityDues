<?php

namespace App\Filament\Resources\MembersResource\RelationManagers;

use App\Models\PaymentItems;
use App\Models\Banks;
use App\Models\Projects;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Get;
use Filament\Forms\Components\Fieldset;
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
                Grid::make([
                    'default' => 2
                ])
                ->schema([
                    Hidden::make('member_id')->afterStateHydrated(function (Hidden $component, $state) {
                        $member_id = $component->getContainer()->getLivewire()->ownerRecord->getAttributeValue('id');
                        if (is_null($state))
                            $state = $member_id;
                        $component->state($state);
                    }),
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
                            ->label('Svrha plaćanja')
                            ->options(PaymentItems::pluck('name', 'id'))
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
                            ->options(Banks::pluck('name', 'id'))
                            ->required(),
                        Select::make('project_id')
                            ->label('Projekat')
                            ->options(Projects::where('is_active', true)->pluck('name', 'id')),
                    ]),
                ])                
            ]);
    }

    protected function getTableActions(): array
    {
        return [
            $this->getEditAction()->hidden(function (?Model $record) {
                return $record->status == 'approved';
            }),
            $this->getApprovedAction()
        ];
    }

    private function getApprovedAction(): Tables\Actions\Action
    {
        return Tables\Actions\Action::make('approved')
            ->label('Odobri')
            ->action(function (?Model $record) {
                $record->status = "approved";
                $record->save();
            })
            ->color('success')
            ->hidden(function (?Model $record) {
                return $record->status == 'approved';
            })
            ->requiresConfirmation();
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
            ->heading('Plaćanja')
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['created_by'] = auth()->id();   
                    $data['total'] = $data['value']; 
                     return $data;
                }),
            ])            
            ->actions([                
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('potvrda_naplate')
                    ->label("Odobri")
                    ->visible(fn (Model $record)=> auth()->user()->hasRole(["admin"]) && $record->status === 'draft')
                    ->action(function(Model $record){
                        $record->updated_by = auth()->user()->id;
                        $record->status = "approved";
                        $record->save();
                    }),
                    Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['updated_by'] = auth()->user()->id;    
                        $data['total'] = $data['value'];    
                         return $data;
                    })
                    ->beforeFormFilled(function (Tables\Actions\EditAction $action, Model $record) {
                        if($record->status == 'draft' && ($record->created_by == auth()->user()->id || auth()->user()->hasRole(["admin"]))){}
                         else  $action->cancel();
                    }),
                    Tables\Actions\DeleteAction::make(),
                ])->visible(function(Model $record){
                    if($record->status == 'draft' && ($record->created_by == auth()->user()->id || auth()->user()->hasRole(["admin"]))){
                        return true;
                    }
                    return false;
                }),
                
            ])            
            ->bulkActions([])
            ->defaultSort('id', 'desc');
    }
}
