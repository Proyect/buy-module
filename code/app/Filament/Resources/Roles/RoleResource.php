<?php

namespace App\Filament\Resources\Roles;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?string $navigationLabel = 'Roles';
    protected static ?string $modelLabel = 'Rol';
    protected static ?string $pluralModelLabel = 'Roles';
    protected static UnitEnum|string|null $navigationGroup = 'Seguridad';
    protected static ?int $navigationSort = 92;

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
            \Filament\Forms\Components\CheckboxList::make('permissions')
                ->label('Permisos')
                ->options(function () {
                    $names = Permission::query()->orderBy('name')->pluck('name')->all();
                    $labels = self::permissionLabels();
                    $options = [];
                    foreach ($names as $name) {
                        $options[$name] = $labels[$name] ?? $name;
                    }
                    return $options;
                })
                ->columns(2)
                ->bulkToggleable()
                ->afterStateHydrated(fn ($state, $record, callable $set) => $set('permissions', $record?->permissions?->pluck('name')->all() ?? []))
                ->dehydrated(true)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable()->sortable()->toggleable(),
                TextColumn::make('guard_name')->label('Guard')->sortable()->toggleable(),
                TextColumn::make('permissions_count')
                    ->label('Permisos')
                    ->counts('permissions')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')->label('Creado')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('Actualizado')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Action::make('save_permissions')
                    ->label('Guardar permisos')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->form([
                        \Filament\Forms\Components\CheckboxList::make('permissions')
                            ->label('Permisos')
                            ->options(function () {
                                $names = Permission::query()->orderBy('name')->pluck('name')->all();
                                $labels = self::permissionLabels();
                                $options = [];
                                foreach ($names as $name) {
                                    $options[$name] = $labels[$name] ?? $name;
                                }
                                return $options;
                            })
                            ->bulkToggleable()
                            ->columns(2)
                            ->default(fn (Role $record) => $record->permissions()->pluck('name')->all()),
                    ])
                    ->action(function (Role $record, array $data) {
                        $record->syncPermissions($data['permissions'] ?? []);
                    })
                    ->visible(fn () => Auth::user()?->hasRole('admin')),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Infolists\Components\TextEntry::make('name')->label('Nombre'),
            \Filament\Infolists\Components\TextEntry::make('guard_name')->label('Guard'),
            \Filament\Infolists\Components\TagsEntry::make('permissions_list')
                ->label('Permisos')
                ->getStateUsing(fn (Role $role) => $role->permissions->pluck('name')->toArray())
                ->columnSpanFull(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRoles::route('/'),
        ];
    }

    private static function permissionLabels(): array
    {
        return [
            'purchase-requests.viewAny' => 'Solicitudes: Ver listado',
            'purchase-requests.view' => 'Solicitudes: Ver detalle',
            'purchase-requests.create' => 'Solicitudes: Crear',
            'purchase-requests.update' => 'Solicitudes: Editar',
            'purchase-requests.delete' => 'Solicitudes: Eliminar',
            'purchase-requests.approve' => 'Solicitudes: Aprobar',
            'purchase-requests.reject' => 'Solicitudes: Rechazar',
            'purchase-requests.export' => 'Solicitudes: Exportar',
        ];
    }
}
