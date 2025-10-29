<?php

namespace App\Filament\Resources\Users;

use App\Models\User;
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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $modelLabel = 'Usuario';
    protected static ?string $pluralModelLabel = 'Usuarios';
    protected static UnitEnum|string|null $navigationGroup = 'Seguridad';
    protected static ?int $navigationSort = 91;

    public static function shouldRegisterNavigation(): bool
    {
        return (bool) (Auth::user()?->hasRole('admin'));
    }

    public static function form(Schema $schema): Schema
    {
        // Mantener esquema mínimo; gestión principal vía acciones
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable()->sortable()->toggleable(),
                TextColumn::make('email')->label('Email')->searchable()->sortable()->toggleable(),
                TagsColumn::make('roles_list')
                    ->label('Roles')
                    ->getStateUsing(fn (User $user) => $user->getRoleNames()->toArray()),
                TextColumn::make('created_at')->label('Creado')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('Actualizado')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Action::make('assign_roles')
                    ->label('Asignar roles')
                    ->icon('heroicon-o-key')
                    ->form([
                        \Filament\Forms\Components\CheckboxList::make('roles')
                            ->label('Roles')
                            ->options(fn () => Role::query()->orderBy('name')->pluck('name', 'name')->all())
                            ->bulkToggleable()
                            ->columns(2)
                            ->required(),
                    ])
                    ->action(function (User $record, array $data) {
                        $record->syncRoles($data['roles'] ?? []);
                    })
                    ->visible(fn () => Auth::user()?->hasRole('admin')),
                Action::make('assign_permissions')
                    ->label('Asignar permisos')
                    ->icon('heroicon-o-shield-check')
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
                            ->columns(2),
                    ])
                    ->action(function (User $record, array $data) {
                        $perms = $data['permissions'] ?? [];
                        $record->syncPermissions($perms);
                    })
                    ->visible(fn () => Auth::user()?->hasRole('admin')),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
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
