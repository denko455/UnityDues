<?php

namespace App\Filament\Clusters\Settings\Resources;

use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Resources\ResidencesResource\Pages;
use App\Filament\Clusters\Settings\Resources\ResidencesResource\RelationManagers;
use App\Models\Residences;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Navigation\NavigationItem;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;

class ResidencesResource extends Resource
{
    protected static ?string $model = Residences::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Settings::class;

    
    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make()
                ->label('Prebivalište')
                ->icon('heroicon-o-banknotes')
                ->sort(1)
                ->url(route('filament.admin.settings.resources.residences.index'))
        ];
    }  

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                TextInput::make('name')->label('Naziv prebivališta')->maxLength(255)->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name')->label('Naziv prebivališta'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResidences::route('/'),
            // 'create' => Pages\CreateResidences::route('/create'),
            // 'edit' => Pages\EditResidences::route('/{record}/edit'),
        ];
    }
}
