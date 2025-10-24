# Manual para Desarrolladores

## Requisitos
- PHP 8.2+, Composer
- Node 18+, npm
- Base de datos (SQLite/MySQL)

## Instalación
```bash
composer install
cp src/buy/.env.example src/buy/.env
php -r "file_exists('src/buy/database/database.sqlite') || touch('src/buy/database/database.sqlite');"
php src/buy/artisan key:generate
php src/buy/artisan migrate --graceful
npm --prefix src/buy install
```

## Ejecución en desarrollo
- Opción 1 (todo en paralelo, requiere `concurrently`):
```bash
composer dev
```
- Opción 2 (manual):
```bash
php src/buy/artisan serve
php src/buy/artisan queue:listen --tries=1
php src/buy/artisan pail --timeout=0
npm --prefix src/buy run dev
```

## Build de assets
```bash
npm --prefix src/buy run build
```

## Estructura
- `src/buy/app/Filament/Resources/`: Recursos de panel admin
- `src/buy/app/Models/`: Eloquent Models
- `src/buy/app/Services/ERPIntegration/`: mapeos y adaptadores ERP
- `src/buy/resources/`: vistas y assets
- `src/buy/routes/`: rutas
- `src/buy/database/`: migraciones y seeders

## Estándares y prácticas
- Formularios Filament v4: `Filament\Schemas\Schema` + componentes (`Tabs`, `TextInput`, `Select`, `Repeater`, etc.)
- Estados de PR: `pending`, `approved`, `rejected`, `completed` (badges y colores en tabla)
- Cálculos reactivos en formularios con `->live()` y `afterStateUpdated`
- Exportables centralizados en `getExportableColumns()` del Resource
- Relaciones eager-loaded para exportaciones y tablas

## Integración ERP
- Añadir campos al mapeo en `app/Services/ERPIntegration/DataMapper.php`
- Adaptar serialización en `PurchaseRequestMapper`/`DynamicMapper`
- Mantener transformaciones bidireccionales explícitas

## Testing
```bash
php src/buy/artisan test
```
- Añadir pruebas de Resources (schemas), mappers ERP y exportaciones

## Estilo de código
```bash
vendor/bin/pint
```

## Troubleshooting
- PDFs en blanco: verificar fuentes y contenido en vista Blade (`resources/views/exports/*.pdf.blade.php`)
- Problemas con productos: formulario alterna entre `product_id` y `custom_name` según `DbSchema::hasTable('products')`
- Rutas Filament: expuestas por el paquete; verificar providers si el panel no carga
