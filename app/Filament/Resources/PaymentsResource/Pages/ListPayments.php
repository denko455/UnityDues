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

use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;

use Filament\Resources\Components\Tab;

use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Query\Builder;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentsResource::class;

    public ?string $tableGroupingDirection = 'desc';


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

    public function getTitle(): string
    {
        return "Plaćanja";
    }

    public function table(Table $table): Table
    {
        $banks = Banks::all()->pluck("name", "id");
        $paymentItems = PaymentItems::all()->pluck("name", "id");
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
                    ->description(fn (Model $record): string => date('d.m.Y', strtotime($record->document_date)))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('member.full_name')
                    ->label('Član')
                    ->default('-')
                    ->sortable(),
                TextColumn::make('payment_item.name')
                    ->label('Razlog plačanja')
                    ->sortable(),
                TextColumn::make('bank.name')
                    ->label('Banka'),
                TextColumn::make('value')->money(fn (Model $record) => $record->currency, true)
                    ->label("Vrijedonst")
                    ->summarize(
                        Summarizer::make()
                            ->label('Ukupno')
                            ->using(function (Builder $query) {
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

                                if ($resEurIn - $resEurOut)
                                    $srt[] = $oFormatter->formatCurrency($resEurIn - $resEurOut, 'EUR');
                                if ($resChfIn - $resChfOut)
                                    $srt[] = $oFormatter->formatCurrency($resChfIn - $resChfOut, 'CHF');
                                if ($resUsdIn - $resUsdOut)
                                    $srt[] = $oFormatter->formatCurrency($resUsdIn - $resUsdOut, 'USD');
                                return implode(', ', $srt);
                            })
                    )
                    ->alignEnd(true)
                    ->sortable(),
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
                    )
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(function ($state) {
                        $statuses = [
                            'draft' => 'Nacrt',
                            'approved' => 'Odobreno',
                        ];
                        return $statuses[$state];
                    })
                    ->badge()
                    ->alignCenter(true)
                    ->color(function ($state) {
                        if ($state === 'draft')
                            return 'gray';
                        if ($state === 'approved')
                            return 'success';
                    })
                    ->sortable()
            ])
            ->defaultSort('document_date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Nacrt',
                        'approved' => 'Odobreno'
                    ]),
                SelectFilter::make('bank_id')
                    ->label('Banka')
                    ->options($banks->all()),
                SelectFilter::make('payment_item_id')
                    ->label('Razlog plačanja')
                    ->options($paymentItems->all())
            ], layout: FiltersLayout::Modal)
            ->bulkActions([])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('potvrda_naplate')
                        ->label("Odobri")
                        ->visible(fn(Model $record) => auth()->user()->hasRole(["admin"]) && $record->status === 'draft')
                        ->action(function (Model $record) {
                            $record->updated_by = auth()->user()->id;
                            $record->status = "approved";
                            $record->save();
                        }),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])

            ])
            ->defaultSort('id', 'desc');
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('Sve'),
            'Ulaz' => Tab::make()->query(fn($query) => $query->select('payments.*')->leftJoin('payment_items', 'payment_item_id', '=', 'payment_items.id')->where('payment_items.type', 'in')),
            'Izlaz' => Tab::make()->query(fn($query) => $query->select('payments.*')->leftJoin('payment_items', 'payment_item_id', '=', 'payment_items.id')->where('payment_items.type', 'out'))
        ];
    }
}
