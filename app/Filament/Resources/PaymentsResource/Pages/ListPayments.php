<?php

namespace App\Filament\Resources\PaymentsResource\Pages;

use App\Filament\Resources\PaymentsResource;
use App\Models\PaymentItems;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

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
                TextColumn::make('document_date')
                    ->label('Datum dokumenta')
                    ->date('d.m.Y')
                    ->sortable(),
                TextColumn::make('payment_item.name')
                    ->label('Razlog plačanja')
                    ->sortable(),
                TextColumn::make('value')->money("EUR", true)
                    ->label("Vrijedonst")
                    ->sortable(),
                TextColumn::make('bank.name')
                    ->label('Banka'),
                TextColumn::make('remarks')->default('-')
                    ->label('Bilješke')
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
            ->filters([])
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
                
            ]);
    }
}
