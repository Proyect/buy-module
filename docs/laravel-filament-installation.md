# Guía de Instalación - Laravel y Filament para Sistema de Compras

## Tabla de Contenidos

1. [Requisitos del Sistema](#requisitos-del-sistema)
2. [Instalación de Laravel](#instalación-de-laravel)
3. [Configuración Inicial](#configuración-inicial)
4. [Instalación de Filament](#instalación-de-filament)
5. [Configuración de Base de Datos](#configuración-de-base-de-datos)
6. [Instalación de Paquetes Adicionales](#instalación-de-paquetes-adicionales)
7. [Configuración de Roles y Permisos](#configuración-de-roles-y-permisos)
8. [Configuración de Filament Shield](#configuración-de-filament-shield)
9. [Estructura de Archivos](#estructura-de-archivos)
10. [Comandos de Desarrollo](#comandos-de-desarrollo)

## Requisitos del Sistema

### Software Requerido
- **PHP**: 8.1 o superior
- **Composer**: 2.0 o superior
- **Node.js**: 16.0 o superior
- **NPM**: 8.0 o superior
- **Base de Datos**: MySQL 8.0, PostgreSQL 13, o SQLite 3.8
- **Servidor Web**: Apache 2.4 o Nginx 1.18

### Extensiones PHP Requeridas
```bash
# Verificar extensiones PHP necesarias
php -m | grep -E "(bcmath|ctype|fileinfo|json|mbstring|openssl|pdo|tokenizer|xml|zip|gd|curl)"
```

Extensiones necesarias:
- bcmath
- ctype
- fileinfo
- json
- mbstring
- openssl
- pdo
- tokenizer
- xml
- zip
- gd (para manejo de imágenes)
- curl (para integración ERP)

## Instalación de Laravel

### 1. Crear Proyecto Laravel

```bash
# Crear nuevo proyecto Laravel
composer create-project laravel/laravel order-buy

# Navegar al directorio del proyecto
cd order-buy

# Verificar instalación
php artisan --version
```

### 2. Configurar Variables de Entorno

```bash
# Copiar archivo de configuración
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### 3. Configurar .env

```env
APP_NAME="Sistema de Compras"
APP_ENV=local
APP_KEY=base64:tu_clave_generada_aqui
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=order_buy_db
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# Configuración ERP
ERP_DB_HOST=127.0.0.1
ERP_DB_PORT=3306
ERP_DB_DATABASE=erp_database
ERP_DB_USERNAME=erp_user
ERP_DB_PASSWORD=erp_password
ERP_COMPANY_ID=1
```

## Configuración Inicial

### 1. Configurar Base de Datos

```bash
# Crear base de datos
mysql -u root -p -e "CREATE DATABASE order_buy_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Ejecutar migraciones
php artisan migrate
```

### 2. Instalar Dependencias Frontend

```bash
# Instalar dependencias NPM
npm install

# Compilar assets
npm run dev
```

### 3. Verificar Instalación

```bash
# Iniciar servidor de desarrollo
php artisan serve

# Verificar en navegador
# http://localhost:8000
```

## Instalación de Filament

### 1. Instalar Filament

```bash
# Instalar Filament
composer require filament/filament

# Instalar Filament con panel de administración
php artisan filament:install --panels
```

### 2. Crear Usuario Administrador

```bash
# Crear usuario administrador
php artisan make:filament-user

# Seguir las instrucciones:
# Name: Admin
# Email: admin@orderbuy.com
# Password: [tu_contraseña_segura]
```

### 3. Verificar Instalación de Filament

```bash
# Acceder al panel de administración
# http://localhost:8000/admin
```

## Configuración de Base de Datos

### 1. Configurar Conexión ERP

```php
// config/database.php
'connections' => [
    'mysql' => [
        'driver' => 'mysql',
        'url' => env('DATABASE_URL'),
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE', 'forge'),
        'username' => env('DB_USERNAME', 'forge'),
        'password' => env('DB_PASSWORD', ''),
        'unix_socket' => env('DB_SOCKET', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => null,
        'options' => extension_loaded('pdo_mysql') ? array_filter([
            PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
        ]) : [],
    ],

    'erp' => [
        'driver' => 'mysql',
        'host' => env('ERP_DB_HOST', '127.0.0.1'),
        'port' => env('ERP_DB_PORT', '3306'),
        'database' => env('ERP_DB_DATABASE', 'forge'),
        'username' => env('ERP_DB_USERNAME', 'forge'),
        'password' => env('ERP_DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => null,
    ],
],
```

### 2. Crear Migraciones

```bash
# Crear migraciones para el sistema de compras
php artisan make:migration create_departments_table
php artisan make:migration create_suppliers_table
php artisan make:migration create_categories_table
php artisan make:migration create_products_table
php artisan make:migration create_purchase_requests_table
php artisan make:migration create_purchase_request_items_table
php artisan make:migration create_purchase_orders_table
php artisan make:migration create_purchase_order_items_table
php artisan make:migration create_approvals_table
php artisan make:migration create_inventory_table
php artisan make:migration create_stock_movements_table
php artisan make:migration create_erp_integration_logs_table
php artisan make:migration create_erp_sync_status_table
php artisan make:migration create_audit_logs_table

# Ejecutar migraciones
php artisan migrate
```

## Instalación de Paquetes Adicionales

### 1. Paquetes de Roles y Permisos

```bash
# Instalar Spatie Laravel Permission
composer require spatie/laravel-permission

# Publicar migraciones
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# Ejecutar migraciones
php artisan migrate
```

### 2. Filament Shield

```bash
# Instalar Filament Shield
composer require bezhansalleh/filament-shield

# Publicar configuración
php artisan vendor:publish --tag=filament-shield-config

# Instalar Shield
php artisan shield:install

# Generar permisos para recursos existentes
php artisan shield:generate --all
```

### 3. Paquetes Adicionales

```bash
# Para notificaciones
composer require laravel/notifications

# Para jobs y queues
composer require laravel/horizon

# Para reportes PDF
composer require barryvdh/laravel-dompdf

# Para logs de actividad
composer require spatie/laravel-activitylog

# Para manejo de archivos
composer require spatie/laravel-medialibrary

# Para validaciones avanzadas
composer require spatie/laravel-validation-rules

# Para caching
composer require spatie/laravel-responsecache
```

### 4. Configurar Servicios

```php
// config/app.php - Agregar providers
'providers' => [
    // ... otros providers
    Spatie\Permission\PermissionServiceProvider::class,
    Spatie\Activitylog\ActivitylogServiceProvider::class,
    Spatie\MediaLibrary\MediaLibraryServiceProvider::class,
    Barryvdh\DomPDF\ServiceProvider::class,
];

// config/app.php - Agregar aliases
'aliases' => [
    // ... otros aliases
    'PDF' => Barryvdh\DomPDF\Facade\Pdf::class,
];
```

## Configuración de Roles y Permisos

### 1. Crear Seeder de Roles y Permisos

```bash
# Crear seeder
php artisan make:seeder RolePermissionSeeder
```

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Resetear cache de roles y permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            // Purchase Request Permissions
            'create-purchase-requests',
            'view-purchase-requests',
            'edit-purchase-requests',
            'delete-purchase-requests',
            'approve-purchase-requests',
            
            // Purchase Order Permissions
            'create-purchase-orders',
            'view-purchase-orders',
            'edit-purchase-orders',
            'delete-purchase-orders',
            
            // Supplier Permissions
            'manage-suppliers',
            'view-suppliers',
            
            // Product Permissions
            'manage-products',
            'view-products',
            
            // Inventory Permissions
            'manage-inventory',
            'view-inventory',
            
            // User Management
            'manage-users',
            'view-users',
            
            // Reports
            'view-reports',
            'export-reports',
            
            // ERP Integration
            'manage-erp-integration',
            'view-erp-logs',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles y asignar permisos
        $roles = [
            'solicitante' => [
                'create-purchase-requests',
                'view-purchase-requests',
                'edit-purchase-requests',
                'view-suppliers',
                'view-products',
            ],
            'comprador' => [
                'create-purchase-requests',
                'view-purchase-requests',
                'edit-purchase-requests',
                'create-purchase-orders',
                'view-purchase-orders',
                'edit-purchase-orders',
                'manage-suppliers',
                'view-suppliers',
                'manage-products',
                'view-products',
                'manage-inventory',
                'view-inventory',
                'view-reports',
            ],
            'supervisor' => [
                'view-purchase-requests',
                'approve-purchase-requests',
                'view-purchase-orders',
                'view-suppliers',
                'view-products',
                'view-inventory',
                'view-reports',
                'view-users',
            ],
            'gerente' => [
                'view-purchase-requests',
                'approve-purchase-requests',
                'view-purchase-orders',
                'view-suppliers',
                'view-products',
                'view-inventory',
                'view-reports',
                'export-reports',
                'view-users',
                'view-erp-logs',
            ],
            'admin' => [
                // Todos los permisos
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::create(['name' => $roleName]);
            
            if ($roleName === 'admin') {
                $role->givePermissionTo(Permission::all());
            } else {
                $role->givePermissionTo($rolePermissions);
            }
        }
    }
}
```

### 2. Ejecutar Seeder

```bash
# Ejecutar seeder
php artisan db:seed --class=RolePermissionSeeder
```

## Configuración de Filament Shield

### 1. Configurar Panel Provider

```php
<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugin(
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ])
            );
    }
}
```

### 2. Configurar Modelos

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_id',
        'department_id',
        'position',
        'phone',
        'manager_id',
        'approval_limit',
        'is_active',
        'erp_user_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'approval_limit' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'department_id', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relaciones
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class);
    }
}
```

## Estructura de Archivos

### Estructura Final del Proyecto

```
order-buy/
├── app/
│   ├── Filament/
│   │   ├── Resources/
│   │   │   ├── PurchaseRequestResource.php
│   │   │   ├── PurchaseOrderResource.php
│   │   │   ├── SupplierResource.php
│   │   │   ├── ProductResource.php
│   │   │   ├── UserResource.php
│   │   │   └── RoleResource.php
│   │   ├── Pages/
│   │   │   ├── Dashboard.php
│   │   │   └── Settings.php
│   │   └── Widgets/
│   │       ├── PendingRequestsWidget.php
│   │       ├── MonthlySpendingWidget.php
│   │       └── TopSuppliersWidget.php
│   ├── Models/
│   │   ├── PurchaseRequest.php
│   │   ├── PurchaseOrder.php
│   │   ├── Supplier.php
│   │   ├── Product.php
│   │   ├── User.php
│   │   └── Department.php
│   ├── Services/
│   │   ├── ERPIntegration/
│   │   │   ├── ERPConnectionService.php
│   │   │   ├── DataSyncService.php
│   │   │   └── MappingService.php
│   │   └── PurchaseService.php
│   ├── Jobs/
│   │   ├── SyncERPDataJob.php
│   │   └── ProcessPurchaseOrderJob.php
│   ├── Events/
│   │   ├── PurchaseRequestSubmitted.php
│   │   └── PurchaseOrderCreated.php
│   └── Listeners/
│       ├── SendApprovalNotification.php
│       └── UpdateInventory.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── RolePermissionSeeder.php
│   │   ├── DepartmentSeeder.php
│   │   ├── SupplierSeeder.php
│   │   └── ProductSeeder.php
│   └── factories/
├── config/
│   ├── filament.php
│   ├── permission.php
│   ├── activitylog.php
│   └── erp_mappings.php
├── resources/
│   ├── views/
│   └── css/
├── docs/
│   ├── erp-integration-mapping.md
│   ├── database-model.md
│   └── laravel-filament-installation.md
└── tests/
    ├── Feature/
    └── Unit/
```

## Comandos de Desarrollo

### 1. Comandos de Filament

```bash
# Crear recursos
php artisan make:filament-resource PurchaseRequest
php artisan make:filament-resource PurchaseOrder
php artisan make:filament-resource Supplier
php artisan make:filament-resource Product
php artisan make:filament-resource User

# Crear widgets
php artisan make:filament-widget PendingRequestsWidget
php artisan make:filament-widget MonthlySpendingWidget
php artisan make:filament-widget TopSuppliersWidget

# Crear páginas personalizadas
php artisan make:filament-page Settings
php artisan make:filament-page Reports

# Generar usuario Filament
php artisan make:filament-user

# Actualizar assets de Filament
php artisan filament:upgrade
```

### 2. Comandos de Shield

```bash
# Generar permisos para todos los recursos
php artisan shield:generate --all

# Generar permisos para un recurso específico
php artisan shield:generate --resource=PurchaseRequestResource

# Super admin
php artisan shield:super-admin --user=1
```

### 3. Comandos de Desarrollo

```bash
# Limpiar caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimizar aplicación
php artisan optimize

# Ejecutar tests
php artisan test

# Verificar configuración
php artisan about
```

### 4. Comandos de Base de Datos

```bash
# Ejecutar migraciones
php artisan migrate

# Rollback migraciones
php artisan migrate:rollback

# Ejecutar seeders
php artisan db:seed

# Ejecutar seeder específico
php artisan db:seed --class=RolePermissionSeeder

# Refrescar base de datos
php artisan migrate:fresh --seed
```

## Verificación de Instalación

### 1. Checklist de Verificación

- [ ] Laravel instalado correctamente
- [ ] Base de datos configurada y migraciones ejecutadas
- [ ] Filament instalado y accesible en `/admin`
- [ ] Usuario administrador creado
- [ ] Spatie Permission instalado
- [ ] Filament Shield configurado
- [ ] Roles y permisos creados
- [ ] Paquetes adicionales instalados
- [ ] Configuración ERP establecida

### 2. URLs de Acceso

- **Aplicación Principal**: http://localhost:8000
- **Panel de Administración**: http://localhost:8000/admin
- **Gestión de Roles**: http://localhost:8000/admin/roles
- **Gestión de Permisos**: http://localhost:8000/admin/permissions

### 3. Comandos de Verificación

```bash
# Verificar versión de Laravel
php artisan --version

# Verificar rutas de Filament
php artisan route:list --name=filament

# Verificar configuración
php artisan config:show filament

# Verificar permisos
php artisan permission:show
```

## Solución de Problemas Comunes

### 1. Error de Permisos

```bash
# Dar permisos de escritura
chmod -R 755 storage bootstrap/cache

# Cambiar propietario (Linux/Mac)
chown -R www-data:www-data storage bootstrap/cache
```

### 2. Error de Base de Datos

```bash
# Verificar conexión
php artisan tinker
>>> DB::connection()->getPdo();

# Verificar migraciones
php artisan migrate:status
```

### 3. Error de Filament

```bash
# Limpiar caches
php artisan filament:clear-cached-components

# Reinstalar Filament
composer reinstall filament/filament
php artisan filament:install --panels
```

---

**Versión**: 1.0  
**Fecha**: Enero 2024  
**Autor**: Sistema de Compras - Guía de Instalación
