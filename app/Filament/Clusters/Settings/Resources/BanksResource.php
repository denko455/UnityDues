<?php

namespace App\Filament\Clusters\Settings\Resources;

use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Resources\BanksResource\Pages;
use App\Models\Banks;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Forms\Components\TextInput;
use Filament\Navigation\NavigationItem;
use Filament\Tables\Columns\TextColumn;

class BanksResource extends Resource
{
    protected static ?string $model = Banks::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Settings::class;

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make()
                ->label('Banka')
                ->icon('heroicon-o-banknotes')
                ->sort(0)
                ->url(route('filament.admin.settings.resources.banks.index'))
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label('Ime Banke'),
                TextInput::make('iban')->label('IBAN'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->default('-')->label('Ime Banke'),
                TextColumn::make('iban')->default('-')->label('IBAN'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
    } 

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanks::route('/')
        ];
    }
}
