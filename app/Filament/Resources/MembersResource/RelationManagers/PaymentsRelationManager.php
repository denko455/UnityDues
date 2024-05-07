<?php

namespace App\Filament\Resources\MembersResource\RelationManagers;

use App\Models\PaymentItems;
use App\Models\Banks;
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
use Filament\Tables\Columns\Summarizers\Sum;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Form $form): Form
    {
        $memberItems = new PaymentItems();
        $items = $memberItems->getMembersPaymentItemsTextList();

        $baks = Banks::getBanks();

        return $form
            ->schema([
                Hidden::make('member_id')->afterStateHydrated(function (Hidden $component, $state) {
                    $member_id = $component->getContainer()->getLivewire()->ownerRecord->getAttributeValue('id');
                    if (is_null($state))
                        $state = $member_id;
                    $component->state($state);
                }),
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
                        // TextInput::make('year')
                        //     ->label('Godina')
                        //     ->numeric()
                        //     ->minValue(2000)
                        //     ->maxValue(9999)
                        //     ->reactive()
                        //     ->afterStateHydrated(function (TextInput $component, $state) {
                        //         $date = null;
                        //         if(!isset($state)) {
                        //             $date = date('Y');
                        //         } else {
                        //             $date = date('Y', strtotime($state));
                        //         }
                        //         $component->state($date);
                        //     })
                        //     ->dehydrateStateUsing(fn ($state) => $state.'-01-01'),
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
                    ->default('-'),
                TextColumn::make('document_date')
                    ->label('Datum dokumenta')
                    ->date('d.m.Y')
                    ->sortable(),
                TextColumn::make('payment_item.name')
                    ->label('Razlog plaćanja'),
                // TextColumn::make('year')
                //     ->label('Godina')
                //     ->formatStateUsing(function($state){
                //         if($state == '-')
                //             return $state;
                //         else return date('Y', strtotime($state));
                //     })
                //     ->default('-')
                //     ->sortable(),
                TextColumn::make('value')
                    ->label('Vrijedonst')
                    ->money('EUR', true)
                    ->summarize(Sum::make()->label('Ukupno')->money('EUR', true))
                    ->alignRight(),
                TextColumn::make('bank.name')
                    ->label('Banka'),
                TextColumn::make('remarks')
                    ->label('Bilješke')
                    ->default('-'),                
                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(function($state){
                        $statuses = [
                            'draft' => 'Nacrt',
                            'approved' => 'Odobreno',
                        ];
                        return $statuses[$state];
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
                Tables\Actions\Action::make('potvrda_naplate')
                ->label("Odobri")
                ->visible(fn (Model $record)=> auth()->user()->hasRole(["admin"]) && $record->status === 'draft')
                ->action(function(Model $record){
                    $record->updated_by = auth()->user()->id;
                    $record->status = "approved";
                    $record->save();
                }),
                Tables\Actions\ActionGroup::make([
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
