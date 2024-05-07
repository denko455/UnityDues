<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsersResource\Pages;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Navigation\NavigationItem;

class UsersResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make()
                ->icon('heroicon-o-user-group')
                ->label('Korisnici')
                ->group('Admin')
                ->sort(1)
                ->url(route('filament.admin.resources.users.index'))
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUsers::route('/create'),
            'edit' => Pages\EditUsers::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(["admin"]);
    }
}
