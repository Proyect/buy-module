<?php

namespace App\Filament\Resources\Permissions;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLockClosed;

    protected static ?string $navigationLabel = 'Permisos';
    protected static ?string $modelLabel = 'Permiso';
    protected static ?string $pluralModelLabel = 'Permisos';
    protected static UnitEnum|string|null $navigationGroup = 'Seguridad';
    protected static ?int $navigationSort = 93;

    public static function shouldRegisterNavigation(): bool
    {
        return (bool) (Auth::user()?->hasRole('admin'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Forms\Components\TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->unique(ignoreRecord: true),
            \Filament\Forms\Components\Select::make('guard_name')
                ->label('Guard')
                ->options(['web' => 'web', 'api' => 'api'])
                ->default('web')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable()->sortable()->toggleable(),
                TextColumn::make('guard_name')->label('Guard')->sortable()->toggleable(),
                TextColumn::make('created_at')->label('Creado')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('Actualizado')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Infolists\Components\TextEntry::make('name')->label('Nombre'),
            \Filament\Infolists\Components\TextEntry::make('guard_name')->label('Guard'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePermissions::route('/'),
        ];
    }
}
