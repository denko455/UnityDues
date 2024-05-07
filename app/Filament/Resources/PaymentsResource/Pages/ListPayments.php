<?php

namespace App\Filament\Resources\PaymentsResource\Pages;

use App\Filament\Resources\PaymentsResource;
use App\Models\PaymentItems;
use App\Models\Banks;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Get;

use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\Summarizers\Sum;

use Filament\Actions\Action;
use Filament\Support\View\Components\Modal;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentsResource::class;

    public function mount(): void
    {
        static::authorizeResourceAccess();
        abort_unless(auth()->user()->hasRole(["admin"]), 403, "Unauthorized action.");
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public  function getTitle(): string
    {
        return "Plaćanja";
    }

    public function table(Table $table): Table
    {
        $banks =  Banks::all()->pluck("name", "id");
        $paymentItems = PaymentItems::all()->pluck("name","id");
        return $table
            ->columns([
                IconColumn::make('payment_item.type')
                    ->label('')
                    ->options([
                        'heroicon-o-arrow-trending-up' => 'in',
                        'heroicon-o-arrow-trending-down' => 'out',
                    ])
                    ->colors([
                        'success' => 'in',
                        'danger' => 'out',
                    ]),                
                TextColumn::make('document_number')
                    ->label('Broj dokumenta')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('')
                    ->label('Član')
                    ->default('-')
                    ->formatStateUsing(function(Model $record){
                        return $record->member->getFullNameAttribute();
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('document_date')
                    ->label('Datum dokumenta')
                    ->date('d.m.Y')
                    ->sortable(),
                TextColumn::make('payment_item.name')
                    ->label('Razlog plačanja')
                    ->sortable(),
                TextColumn::make('value')->money("EUR", true)
                    ->label("Vrijedonst")
                    ->summarize(Sum::make()->label('Ukupno')->money('EUR', true))
                    ->sortable(),
                TextColumn::make('bank.name')
                    ->label('Banka'),
                IconColumn::make('remarks')
                    ->label('Bilješke')
                    ->icon(function($state){
                        return $state ? 'heroicon-o-envelope' :'';
                    })
                    ->hidden(fn($state) => !empty($state))
                    ->action(
                        Tables\Actions\Action::make('message')
                        ->modalHeading('Bilješka')
                        ->modalSubmitAction(false) 
                        ->requiresConfirmation()
                        ->modalIcon(null)
                        ->modalDescription(function (Model $record){   
                            return $record->remarks;
                        })                        
                    )
                    ->searchable(),
                
                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(function($state){
                        $statuses = [
                            'draft' => 'Nacrt',
                            'approved' => 'Odobreno',
                        ];
                        return $statuses[$state];
                    })
                    ->sortable()
            ])
            ->defaultSort('document_date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Nacrt',
                        'approved' => 'Odobreno']),
                SelectFilter::make('bank_id')
                    ->label('Banka')
                    ->options($banks->all()),
                SelectFilter::make('payment_item_id')
                    ->label('Razlog plačanja')
                    ->options($paymentItems->all())
            ], layout: FiltersLayout::Modal)
            ->bulkActions([])
            ->actions([
                Tables\Actions\Action::make('potvrda_naplate')
                ->label("Odobri")
                ->visible(fn (Model $record)=> auth()->user()->hasRole(["admin"]) && $record->status === 'draft')
                ->action(function(Model $record){
                    $record->updated_by = auth()->user()->id;
                    $record->status = "approved";
                    $record->save();
                }),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                
            ])
            ->defaultSort('id', 'desc');
    }
}
