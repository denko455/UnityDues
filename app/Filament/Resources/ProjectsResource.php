<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectsResource\Pages;
use App\Models\Projects;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Filament\Resources\ProjectsResource\RelationManagers;


use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Checkbox;


use Filament\Navigation\NavigationItem;


class ProjectsResource extends Resource
{
    protected static ?string $model = Projects::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make()
                ->label('Projecti')
                ->icon('heroicon-o-bars-3-center-left')
                ->group('Admin')
                ->sort(1)
                ->url(route('filament.admin.resources.projects.index'))
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(["admin"]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Ime projekta')
                    ->required(),
                Textarea::make('description')
                    ->label('Opis'),
                Checkbox::make('is_active')
                    ->default(true)
                    ->label('Aktivan'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'edit' => Pages\EditProjects::route('/{record}/edit'),
        ];
    }
}
