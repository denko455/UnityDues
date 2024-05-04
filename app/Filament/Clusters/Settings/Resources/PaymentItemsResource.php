<?php

namespace App\Filament\Clusters\Settings\Resources;

use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Resources\PaymentItemsResource\Pages;
use App\Filament\Clusters\Settings\Resources\PaymentItemsResource\RelationManagers;
use App\Models\PaymentItems;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Navigation\NavigationItem;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;

class PaymentItemsResource extends Resource
{
    protected static ?string $model = PaymentItems::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Settings::class;

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make()
                ->label('Razlozi plačanja')
                ->icon('heroicon-o-banknotes')
                ->sort(1)
                ->url(route('filament.admin.settings.resources.payment-items.index'))
        ];
    }

    
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                TextInput::make('name')->label('Ime')->maxLength(255)->required(),
                Select::make('type')->label('U/I')
                ->options([
                    'in' => 'Ulaz',
                    'out' => 'Izlaz',
                ])
                ->required(),
                Checkbox::make('to_members')->label('Kod članova')->inline(),
                Checkbox::make('to_payment')->label('Kod plaćanja')->inline(),
                Textarea::make('description')->label('Bilješka')->maxLength(255)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name')->label('Ime'),
                TextColumn::make('type')->label('U/I')->formatStateUsing(function(string $state){
                    if($state === 'in'){
                        return 'Ulaz';
                    } else if($state === 'out'){
                        return 'Izlaz';
                    } 
                    return '-';
                }),
                IconColumn::make('to_members')->label('Kod članova')
                ->icon(fn (string $state): string => match ($state) {
                    '0' => 'heroicon-o-minus',
                    '1' => 'heroicon-o-check',
                    NULL => 'heroicon-o-minus',
                }),
                IconColumn::make('to_payments')->label('Kod plaćanja')
                ->icon(fn (string $state): string => match ($state) {
                    '0' => 'heroicon-o-minus',
                    '1' => 'heroicon-o-check',
                     default => 'heroicon-o-minus',
                }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentItems::route('/')
        ];
    }
}
