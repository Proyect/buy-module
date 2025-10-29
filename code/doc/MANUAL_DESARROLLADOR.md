# 📚 Manual del Desarrollador - Sistema de Compras

## 🎯 **Información General**

**Proyecto:** Sistema de Compras Empresarial  
**Framework:** Laravel 12.0 + Filament 4.1  
**Base de Datos:** MySQL 8.0+  
**Idioma:** Español (Argentina)  
**Moneda:** Pesos Argentinos (ARS)  
**Versión:** 1.0.0  

---

## 🚀 **Configuración del Entorno de Desarrollo**

### **Requisitos del Sistema**
- PHP 8.2+
- Composer 2.0+
- MySQL 8.0+
- Node.js 18+
- XAMPP (opcional, para MySQL)

### **Instalación Inicial**

```bash
# 1. Clonar el repositorio
git clone [url-del-repositorio]
cd buy-module/src/buy

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias Node.js
npm install

# 4. Configurar archivo de entorno
cp .env.example .env

# 5. Generar clave de aplicación
php artisan key:generate

# 6. Configurar base de datos en .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=buy_module
DB_USERNAME=root
DB_PASSWORD=

# 7. Ejecutar migraciones
php artisan migrate

# 8. Poblar con datos de prueba
php artisan db:seed

# 9. Iniciar servidor de desarrollo
php artisan serve --port=5003

# 10. Iniciar Vite (en otra terminal)
npm run dev
```

### **URLs del Sistema**
- **Aplicación:** http://localhost:5003
- **Panel Admin:** http://localhost:5003/admin
- **phpMyAdmin:** http://localhost/phpmyadmin (si usas XAMPP)

---

## 🏗️ **Arquitectura del Sistema**

### **Estructura de Directorios**

```
buy/
├── app/
│   ├── Console/Commands/          # Comandos Artisan personalizados
│   ├── Filament/
│   │   ├── Resources/             # Recursos Filament (CRUD)
│   │   ├── Pages/                 # Páginas personalizadas
│   │   └── Widgets/               # Widgets del dashboard
│   ├── Models/                    # Modelos Eloquent
│   ├── Policies/                  # Políticas de autorización
│   ├── Services/                  # Lógica de negocio
│   └── Providers/                 # Service Providers
├── database/
│   ├── factories/                 # Factories para testing
│   ├── migrations/                # Migraciones de BD
│   └── seeders/                   # Seeders de datos
├── resources/
│   ├── lang/es/                   # Traducciones en español
│   ├── views/                     # Vistas Blade
│   └── css/                       # Estilos CSS
├── config/                        # Archivos de configuración
└── tests/                         # Tests automatizados
```

### **Patrones de Diseño Utilizados**

1. **Repository Pattern** - Para acceso a datos
2. **Service Layer** - Para lógica de negocio
3. **Policy Pattern** - Para autorización
4. **Factory Pattern** - Para testing
5. **Observer Pattern** - Para eventos de modelo

---

## 📋 **Metodología de Desarrollo**

### **Los 7 Pasos por Módulo**

Cada nuevo módulo debe seguir esta metodología:

#### **Paso 1: Migración de Base de Datos** 🗄️
```bash
php artisan make:migration create_[tabla]_table
```

**Ejemplo:**
```php
Schema::create('suppliers', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('code', 50)->unique();
    $table->string('email')->unique();
    $table->string('phone')->nullable();
    $table->text('address')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    // Índices para optimización
    $table->index('code');
    $table->index('is_active');
});
```

#### **Paso 2: Modelo Eloquent** 🏗️
```bash
php artisan make:model Supplier -mfsp
```

**Ejemplo:**
```php
class Supplier extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 'code', 'email', 'phone', 
        'address', 'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    // Relaciones
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

#### **Paso 3: Factory** 🏭
```php
class SupplierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'code' => 'SUP' . fake()->unique()->numberBetween(1000, 9999),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'is_active' => fake()->boolean(90),
        ];
    }
}
```

#### **Paso 4: Seeder** 🌱
```php
class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        // Datos maestros
        $masterSuppliers = [
            [
                'name' => 'Proveedor Principal S.A.',
                'code' => 'SUP001',
                'email' => 'contacto@proveedor-principal.com',
                'is_active' => true,
            ],
            // ... más proveedores
        ];
        
        foreach ($masterSuppliers as $supplier) {
            Supplier::create($supplier);
        }
        
        // Datos de prueba
        if (app()->environment('local')) {
            Supplier::factory(10)->create();
        }
    }
}
```

#### **Paso 5: Recurso Filament** 🎨
```bash
php artisan make:filament-resource Supplier
```

**Configuración básica:**
```php
class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Proveedores';
    protected static ?string $modelLabel = 'Proveedor';
    protected static ?string $pluralModelLabel = 'Proveedores';
    
    // Implementar form(), table(), infolist()
}
```

#### **Paso 6: Políticas de Acceso** 🔒
```bash
php artisan make:policy SupplierPolicy --model=Supplier
```

#### **Paso 7: Testing** ✅
```bash
php artisan make:test SupplierTest
```

---

## 🎨 **Estándares de Código**

### **Convenciones de Nomenclatura**

| Elemento | Convención | Ejemplo |
|----------|------------|---------|
| **Tablas** | snake_case, plural | `purchase_requests` |
| **Modelos** | PascalCase, singular | `PurchaseRequest` |
| **Controladores** | PascalCase + Controller | `PurchaseRequestController` |
| **Variables** | camelCase | `$purchaseRequest` |
| **Constantes** | UPPER_SNAKE_CASE | `STATUS_PENDING` |
| **Métodos** | camelCase | `calculateTotal()` |
| **Archivos** | PascalCase | `PurchaseRequestService.php` |

### **Estructura de Clases**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo para gestionar proveedores del sistema
 * 
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $email
 * @property string|null $phone
 * @property string|null $address
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Supplier extends Model
{
    use HasFactory;

    /**
     * Atributos que pueden ser asignados masivamente
     */
    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'address',
        'is_active',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Obtiene todos los productos de este proveedor
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope para filtrar solo proveedores activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

### **Comentarios y Documentación**

```php
/**
 * Calcula el total de una solicitud de compra
 * 
 * @param Collection $items Items de la solicitud
 * @param float $taxRate Tasa de impuestos (opcional)
 * @return array Array con subtotal, impuestos y total
 * 
 * @throws InvalidArgumentException Si los items están vacíos
 */
public function calculateTotal(Collection $items, float $taxRate = 0.21): array
{
    if ($items->isEmpty()) {
        throw new InvalidArgumentException('Los items no pueden estar vacíos');
    }

    $subtotal = $items->sum('total');
    $taxes = $subtotal * $taxRate;
    $total = $subtotal + $taxes;

    return [
        'subtotal' => $subtotal,
        'taxes' => $taxes,
        'total' => $total,
    ];
}
```

---

## 🔧 **Configuración Regional (Argentina)**

### **Idioma y Localización**

```php
// config/app.php
'locale' => 'es',
'fallback_locale' => 'es',
'faker_locale' => 'es_ES',
'timezone' => 'America/Argentina/Buenos_Aires',
```

### **Formato de Moneda**

```php
// Formato argentino: $1.000.000,00 ARS
->formatStateUsing(fn ($state) => '$' . number_format($state, 2, ',', '.') . ' ARS')

// Configuración en config/currency.php
'ARS' => [
    'name' => 'Peso Argentino',
    'symbol' => '$',
    'code' => 'ARS',
    'decimal_places' => 2,
    'thousands_separator' => '.',
    'decimal_separator' => ',',
    'symbol_position' => 'before',
],
```

### **Rangos de Presupuestos por Departamento**

```php
const BUDGET_RANGES = [
    'Recursos Humanos' => ['min' => 5000000, 'max' => 50000000],      // $5M - $50M
    'Finanzas' => ['min' => 10000000, 'max' => 100000000],            // $10M - $100M
    'Compras' => ['min' => 50000000, 'max' => 500000000],             // $50M - $500M
    'Ventas' => ['min' => 20000000, 'max' => 200000000],              // $20M - $200M
    'Marketing' => ['min' => 10000000, 'max' => 80000000],            // $10M - $80M
    'Tecnología' => ['min' => 30000000, 'max' => 150000000],          // $30M - $150M
    'Operaciones' => ['min' => 100000000, 'max' => 1000000000],       // $100M - $1B
    'Logística' => ['min' => 20000000, 'max' => 100000000],           // $20M - $100M
    'Legal' => ['min' => 2000000, 'max' => 20000000],                 // $2M - $20M
    'Administración' => ['min' => 5000000, 'max' => 30000000],        // $5M - $30M
];
```

---

## 🔐 **Sistema de Permisos y Roles**

### **Roles del Sistema**

```php
const ROLES = [
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
```

### **Permisos por Módulo**

```php
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

### **Implementación de Políticas**

```php
class DepartmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([
            'view_any_department',
            'admin_access',
            'gerente_general_access'
        ]);
    }

    public function manageBudget(User $user, Department $department): bool
    {
        // Solo roles específicos pueden gestionar presupuestos
        return $user->hasAnyPermission([
            'manage_department_budget',
            'admin_access',
            'gerente_general_access',
            'contador_access'
        ]);
    }
}
```

---

## 🧪 **Testing**

### **Estructura de Tests**

```
tests/
├── Feature/                    # Tests de integración
│   ├── DepartmentTest.php
│   ├── SupplierTest.php
│   └── PurchaseRequestTest.php
├── Unit/                       # Tests unitarios
│   ├── DepartmentServiceTest.php
│   └── BudgetCalculatorTest.php
└── Browser/                    # Tests de navegador
    └── DepartmentManagementTest.php
```

### **Ejemplo de Test de Feature**

```php
<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_department()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->post('/admin/departments', [
                'name' => 'Recursos Humanos',
                'code' => 'RH01',
                'description' => 'Departamento de RRHH',
                'budget_limit' => 10000000,
                'is_active' => true,
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('departments', [
            'name' => 'Recursos Humanos',
            'code' => 'RH01',
        ]);
    }

    public function test_cannot_exceed_budget_limit()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->post('/admin/departments', [
                'name' => 'Test Department',
                'code' => 'TEST01',
                'budget_limit' => 1000000000, // $1B - excede límite
            ]);

        $response->assertSessionHasErrors(['budget_limit']);
    }
}
```

### **Comandos de Testing**

```bash
# Ejecutar todos los tests
php artisan test

# Ejecutar tests específicos
php artisan test --filter=DepartmentTest

# Ejecutar con coverage
php artisan test --coverage

# Tests de navegador
php artisan dusk
```

---

## 🚀 **Comandos Útiles**

### **Desarrollo**

```bash
# Crear módulo completo
php artisan make:model Supplier -mfsp
php artisan make:filament-resource Supplier
php artisan make:policy SupplierPolicy --model=Supplier

# Limpiar caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **Base de Datos**

```bash
# Migraciones
php artisan migrate
php artisan migrate:rollback
php artisan migrate:refresh
php artisan migrate:fresh --seed

# Seeders específicos
php artisan db:seed --class=DepartmentSeeder
php artisan db:seed --class=SupplierSeeder

# Generar datos de prueba
php artisan tinker
>>> Department::factory(10)->create()
```

### **Filament**

```bash
# Generar recurso completo
php artisan make:filament-resource Supplier --generate

# Generar página personalizada
php artisan make:filament-page Settings

# Generar widget
php artisan make:filament-widget DepartmentStats

# Generar tema personalizado
php artisan make:filament-theme
```

---

## 📊 **Debugging y Logging**

### **Configuración de Logs**

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
],
```

### **Uso de Logs**

```php
use Illuminate\Support\Facades\Log;

// Log de información
Log::info('Nuevo departamento creado', [
    'department_id' => $department->id,
    'name' => $department->name,
    'user_id' => auth()->id(),
]);

// Log de error
Log::error('Error al procesar solicitud de compra', [
    'request_id' => $request->id,
    'error' => $exception->getMessage(),
    'trace' => $exception->getTraceAsString(),
]);
```

### **Debugging con Tinker**

```bash
php artisan tinker

# Ejemplos de uso
>>> $department = Department::first();
>>> $department->budget_limit
>>> $department->manager
>>> Department::active()->get()
>>> Department::where('budget_limit', '>', 10000000)->get()
```

---

## 🔧 **Troubleshooting**

### **Problemas Comunes**

#### **Error de Conexión a Base de Datos**
```bash
# Verificar configuración
php artisan config:show database

# Probar conexión
php artisan tinker
>>> DB::connection()->getPdo()
```

#### **Error 404 en Filament**
```bash
# Limpiar rutas
php artisan route:clear
php artisan config:clear

# Verificar rutas
php artisan route:list | grep filament
```

#### **Problemas de Permisos**
```bash
# Regenerar permisos
php artisan shield:generate --all

# Verificar roles
php artisan tinker
>>> User::first()->roles
>>> User::first()->permissions
```

### **Logs de Error**

```bash
# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Filtrar errores específicos
grep "ERROR" storage/logs/laravel.log
```

---

## 📚 **Recursos Adicionales**

### **Documentación Oficial**
- [Laravel 12.x](https://laravel.com/docs/12.x)
- [Filament 4.x](https://filamentphp.com/docs)
- [Spatie Permission](https://spatie.be/docs/laravel-permission)

### **Herramientas de Desarrollo**
- **IDE:** PhpStorm, VS Code
- **Debugging:** Xdebug, Laravel Debugbar
- **API Testing:** Postman, Insomnia
- **Database:** phpMyAdmin, TablePlus

### **Convenciones del Proyecto**
- **Commits:** Conventional Commits
- **Branches:** GitFlow
- **Code Review:** Obligatorio para PRs
- **Testing:** Coverage mínimo 70%

---

## 📞 **Contacto y Soporte**

**Desarrollador Principal:** [Tu Nombre]  
**Email:** [tu-email@empresa.com]  
**Slack:** #desarrollo-compras  
**Documentación:** [URL del wiki interno]  

---

**Versión del Manual:** 1.0  
**Última Actualización:** Octubre 2025  
**Próxima Revisión:** Noviembre 2025  

---

*Este manual es un documento vivo que se actualiza con cada nueva funcionalidad del sistema. Mantén siempre la versión más reciente.*


