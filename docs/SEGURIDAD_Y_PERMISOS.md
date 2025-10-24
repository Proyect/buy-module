# Seguridad y Permisos (Laravel 12 + Filament 4 + Spatie Permission)

## Resumen

- Se implementó control de acceso basado en roles y permisos (RBAC) con `spatie/laravel-permission`.
- Se añadieron validaciones de permisos en las acciones de `PurchaseRequest` dentro de Filament.
- Se crearon seeders para roles y permisos base y un usuario demo con rol `admin`.

## Cambios realizados

- Archivo modificado: `src/buy/app/Models/User.php`
  - Se añadió el trait `Spatie\Permission\Traits\HasRoles`.
- Archivo modificado: `src/buy/app/Filament/Resources/PurchaseRequests/PurchaseRequestResource.php`
  - Se añadieron checks `->visible(...)` y `->authorize(...)` para acciones:
    - `approve`, `reject`, `export_all_csv`, `export_all_pdf`, `export_selected_csv`, `export_selected_pdf`, `EditAction`, `DeleteAction`, `DeleteBulkAction`, `approve_selected`, `reject_selected`, `complete_selected`.
  - Permisos usados: `purchase-requests.approve`, `.reject`, `.export`, `.update`, `.delete`.
- Archivos nuevos (seeders):
  - `src/buy/database/seeders/RolesAndPermissionsSeeder.php`
  - `src/buy/database/seeders/UserRolesDemoSeeder.php`
- Archivo actualizado (orquestación de seeders):
  - `src/buy/database/seeders/DatabaseSeeder.php`

## Roles y permisos

- Permisos creados:
  - `purchase-requests.viewAny`, `purchase-requests.view`, `purchase-requests.create`, `purchase-requests.update`, `purchase-requests.delete`, `purchase-requests.approve`, `purchase-requests.reject`, `purchase-requests.export`.
- Roles base:
  - `solicitante`: viewAny, view, create, update
  - `aprobador`: viewAny, view, approve, reject
  - `compras`: viewAny, view, update, export
  - `admin`: todos los permisos

## Comandos a ejecutar (PowerShell)

1) Instalar paquete (ya ejecutado si figura en `composer.json`):
```
composer require spatie/laravel-permission:^6.12 --no-interaction
```

2) Publicar config y migraciones de Spatie:
```
php artisan vendor:publish --provider=Spatie\Permission\PermissionServiceProvider --tag=permission-config --tag=permission-migrations --ansi
```

3) Migrar base de datos:
```
php artisan migrate --ansi
```

4) Sembrar datos (roles/permisos y usuario demo con rol admin):
```
php artisan db:seed --class=Database\Seeders\DatabaseSeeder --ansi
```

5) (Opcional) Limpiar cachés:
```
php artisan optimize:clear --ansi
```

## Asignación de roles a usuarios

Ejemplos en Tinker:
```
php artisan tinker
>>> $u = App\Models\User::firstWhere('email','test@example.com');
>>> $u->assignRole('aprobador');
>>> $u->syncRoles(['compras']);
```

## Consideraciones en Filament

- Acciones protegidas con `auth()->user()->can('permiso')`:
  - Aprobación/Rechazo: requieren `purchase-requests.approve`/`purchase-requests.reject`.
  - Exportaciones: requieren `purchase-requests.export`.
  - Edición/Eliminación: requieren `purchase-requests.update`/`purchase-requests.delete`.
- Si los permisos no existen o no están asignados, las acciones se ocultan y/o bloquean.

## Pruebas sugeridas

- Ingresar con `test@example.com` (tiene rol `admin`) y verificar acceso a todas las acciones.
- Crear otro usuario sin roles y validar que no ve acciones de aprobar/rechazar/exportar.
- Asignar rol `aprobador` y validar sólo las acciones de aprobación/rechazo.
- Asignar rol `compras` y validar exportaciones y completar.

## Reversión

- Remover trait `HasRoles` de `User` y líneas añadidas.
- Quitar checks `->authorize/->visible` en `PurchaseRequestResource`.
- (Opcional) Revertir migraciones de Spatie: `php artisan migrate:rollback`.

## Referencias

- Paquete: https://github.com/spatie/laravel-permission
- Filament v4 (Schemas): https://filamentphp.com/docs
