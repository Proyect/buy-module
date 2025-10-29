# ⚙️ Configuración de Desarrollo - Sistema de Compras

## 🎯 **Configuración Inicial del Entorno**

### **1. Variables de Entorno (.env)**

```env
# Aplicación
APP_NAME="Sistema de Compras"
APP_ENV=local
APP_KEY=base64:[clave-generada]
APP_DEBUG=true
APP_URL=http://localhost:5003

# Idioma y Región (Argentina)
APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_FAKER_LOCALE=es_ES
APP_TIMEZONE=America/Argentina/Buenos_Aires

# Base de Datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=buy_module
DB_USERNAME=root
DB_PASSWORD=

# Cache y Sesiones
CACHE_STORE=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Mail (desarrollo)
MAIL_MAILER=log
MAIL_FROM_ADDRESS="compras@empresa.com"
MAIL_FROM_NAME="${APP_NAME}"

# Vite
VITE_APP_NAME="${APP_NAME}"
```

### **2. Configuración de Base de Datos**

```php
// config/database.php
'connections' => [
    'mysql' => [
        'driver' => 'mysql',
        'url' => env('DATABASE_URL'),
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE', 'buy_module'),
        'username' => env('DB_USERNAME', 'root'),
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
],
```

### **3. Configuración de Moneda (Argentina)**

```php
// config/currency.php
return [
    'default' => 'ARS',
    
    'currencies' => [
        'ARS' => [
            'name' => 'Peso Argentino',
            'symbol' => '$',
            'code' => 'ARS',
            'decimal_places' => 2,
            'thousands_separator' => '.',
            'decimal_separator' => ',',
            'symbol_position' => 'before',
            'format' => '{symbol} {amount} {code}',
        ],
    ],
    
    'format' => [
        'locale' => 'es_AR',
        'currency' => 'ARS',
        'symbol' => '$',
        'decimal_places' => 2,
        'thousands_separator' => '.',
        'decimal_separator' => ',',
    ],
];
```

## 🎨 **Configuración de Filament**

### **1. Panel de Administración**

```php
// app/Providers/Filament/AdminPanelProvider.php
public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->path('admin')
        ->login()
        ->authGuard('web')
        ->colors([
            'primary' => Color::Blue,
        ])
        // Configuración del sidebar
        ->sidebarCollapsibleOnDesktop()
        ->sidebarWidth('16rem')
        ->sidebarFullyCollapsibleOnDesktop()
        // Configuración de navegación
        ->navigationGroups([
            'Gestión' => 'heroicon-o-cog-6-tooth',
            'Catálogos' => 'heroicon-o-squares-2x2',
            'Procesos' => 'heroicon-o-arrow-path',
            'Reportes' => 'heroicon-o-chart-bar',
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
        ]);
}
```

### **2. Configuración de Recursos**

```php
// Ejemplo: DepartmentResource.php
class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $recordTitleAttribute = 'name';
    
    // Textos en español
    protected static ?string $navigationLabel = 'Departamentos';
    protected static ?string $modelLabel = 'Departamento';
    protected static ?string $pluralModelLabel = 'Departamentos';
    
    // Agrupación de navegación
    protected static ?string $navigationGroup = 'Catálogos';
    protected static ?int $navigationSort = 1;
}
```

## 🔐 **Configuración de Permisos**

### **1. Instalación de Spatie Permission**

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### **2. Configuración de Roles**

```php
// database/seeders/RolePermissionSeeder.php
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Crear roles
        $roles = [
            'super_admin' => 'Super Administrador',
            'admin' => 'Administrador',
            'gerente_general' => 'Gerente General',
            'gerente_departamento' => 'Gerente de Departamento',
            'coordinador_compras' => 'Coordinador de Compras',
            'analista_compras' => 'Analista de Compras',
            'solicitante' => 'Solicitante',
            'aprobador' => 'Aprobador',
            'contador' => 'Contador',
            'auditor' => 'Auditor',
            'viewer' => 'Solo Lectura',
        ];
        
        foreach ($roles as $name => $displayName) {
            Role::create(['name' => $name, 'display_name' => $displayName]);
        }
    }
}
```

### **3. Configuración de Permisos**

```php
// Permisos por módulo
const MODULE_PERMISSIONS = [
    'departments' => [
        'view_any_department',
        'view_department',
        'create_department',
        'update_department',
        'delete_department',
        'manage_department_budget',
        'view_department_reports',
    ],
    'suppliers' => [
        'view_any_supplier',
        'view_supplier',
        'create_supplier',
        'update_supplier',
        'delete_supplier',
        'manage_supplier_contracts',
    ],
    // ... más módulos
];
```

## 🧪 **Configuración de Testing**

### **1. Configuración de Base de Datos de Testing**

```php
// config/database.php
'connections' => [
    'testing' => [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
    ],
],
```

### **2. Configuración de PHPUnit**

```xml
<!-- phpunit.xml -->
<phpunit>
    <testsuite name="Unit">
        <directory>./tests/Unit</directory>
    </testsuite>
    <testsuite name="Feature">
        <directory>./tests/Feature</directory>
    </testsuite>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="DB_CONNECTION" value="testing"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="SESSION_DRIVER" value="array"/>
    </php>
</phpunit>
```

### **3. Factories para Testing**

```php
// database/factories/DepartmentFactory.php
class DepartmentFactory extends Factory
{
    protected $model = Department::class;
    
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'code' => 'DEPT' . fake()->unique()->numberBetween(100, 999),
            'description' => fake()->sentence(),
            'budget_limit' => fake()->randomFloat(2, 1000000, 100000000),
            'is_active' => fake()->boolean(90),
        ];
    }
    
    public function highBudget(): static
    {
        return $this->state(fn (array $attributes) => [
            'budget_limit' => fake()->randomFloat(2, 50000000, 500000000),
        ]);
    }
}
```

## 📊 **Configuración de Logging**

### **1. Canales de Log Personalizados**

```php
// config/logging.php
'channels' => [
    'single' => [
        'driver' => 'single',
        'path' => storage_path('logs/laravel.log'),
        'level' => 'debug',
    ],
    'purchases' => [
        'driver' => 'single',
        'path' => storage_path('logs/purchases.log'),
        'level' => 'info',
    ],
    'audit' => [
        'driver' => 'single',
        'path' => storage_path('logs/audit.log'),
        'level' => 'info',
    ],
],
```

### **2. Uso de Logs en el Código**

```php
use Illuminate\Support\Facades\Log;

// Log de información
Log::channel('purchases')->info('Nueva solicitud de compra creada', [
    'request_id' => $request->id,
    'user_id' => auth()->id(),
    'amount' => $request->total_amount,
]);

// Log de auditoría
Log::channel('audit')->info('Presupuesto modificado', [
    'department_id' => $department->id,
    'old_budget' => $oldBudget,
    'new_budget' => $newBudget,
    'user_id' => auth()->id(),
]);
```

## 🎨 **Configuración de Vite**

### **1. Configuración de Vite**

```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from 'tailwindcss';

export default defineConfig({
    server: {
        port: 5174,
        host: 'localhost',
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        outDir: 'public/build',
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['vue', 'alpinejs'],
                },
            },
        },
    },
});
```

### **2. Configuración de Tailwind CSS**

```javascript
// tailwind.config.js
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./app/Filament/**/*.php",
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#eff6ff',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                },
            },
        },
    },
    plugins: [],
};
```

## 🔧 **Configuración de Desarrollo**

### **1. Configuración de Xdebug (Opcional)**

```ini
; php.ini
[xdebug]
zend_extension=xdebug
xdebug.mode=debug
xdebug.start_with_request=yes
xdebug.client_host=127.0.0.1
xdebug.client_port=9003
xdebug.idekey=PHPSTORM
```

### **2. Configuración de VS Code**

```json
// .vscode/settings.json
{
    "php.suggest.basic": false,
    "php.validate.enable": true,
    "php.validate.executablePath": "C:\\xampp\\php\\php.exe",
    "files.associations": {
        "*.blade.php": "blade"
    },
    "emmet.includeLanguages": {
        "blade": "html"
    }
}
```

## 📱 **Configuración de Responsive**

### **1. Breakpoints de Filament**

```php
// En recursos Filament
->columns([
    TextColumn::make('name')
        ->label('Nombre')
        ->searchable()
        ->sortable()
        ->toggleable()
        ->visibleOn(['tablet', 'desktop']),
        
    TextColumn::make('code')
        ->label('Código')
        ->searchable()
        ->sortable()
        ->visibleOn(['mobile', 'tablet', 'desktop']),
])
```

## 🚀 **Configuración de Producción**

### **1. Variables de Entorno de Producción**

```env
# Producción
APP_ENV=production
APP_DEBUG=false
APP_URL=https://compras.empresa.com

# Base de datos de producción
DB_CONNECTION=mysql
DB_HOST=prod-db.empresa.com
DB_DATABASE=compras_prod
DB_USERNAME=compras_user
DB_PASSWORD=[password-seguro]

# Cache y sesiones
CACHE_STORE=redis
SESSION_DRIVER=redis
SESSION_SECURE_COOKIE=true

# Mail de producción
MAIL_MAILER=smtp
MAIL_HOST=smtp.empresa.com
MAIL_PORT=587
MAIL_USERNAME=compras@empresa.com
MAIL_PASSWORD=[password-email]
MAIL_ENCRYPTION=tls
```

### **2. Optimizaciones de Producción**

```bash
# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Compilar assets
npm run build

# Limpiar caches de desarrollo
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

**Nota:** Esta configuración está optimizada para el contexto argentino y el stack tecnológico del proyecto. Ajusta según las necesidades específicas de tu entorno.


