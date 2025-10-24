# Informe técnico del módulo de Compras

## Resumen
- **Framework**: Laravel 12 (PHP ^8.2)
- **Admin UI**: Filament 4.1 (Schemas v4)
- **Exportación**: CSV y PDF (DomPDF)
- **Dominio**: Solicitudes de compra, Ítems, Departamentos, Productos

## Estructura del proyecto
- `src/buy/app/`
  - `Filament/Resources/`
  - `Models/`
  - `Services/ERPIntegration/`
- `src/buy/routes/`
- `src/buy/database/migrations/`
- `resources/views/exports/`

## Dependencias clave (`src/buy/composer.json`)
- `laravel/framework:^12.0`
- `filament/filament:^4.1`
- `barryvdh/laravel-dompdf:^3.1`
- Dev: `phpunit`, `pint`, `sail`, `pail`

## Frontend (`src/buy/package.json`)
- Vite 7, Tailwind 4
- Scripts: `npm run dev`, `npm run build`

## Dominio y Modelos
- `App\Models\PurchaseRequest`:
  - Fillable: `request_number`, `user_id`, `department_id`, `request_date`, `required_date`, `priority`, `status`, `total_amount`, `currency`, `justification`, `notes`, `approved_by`, `approved_at`, `rejected_by`, `rejected_at`, `rejection_reason`, `erp_request_id`
  - Relaciones: `user()`, `department()`, `approvedBy()`, `rejectedBy()`, `items()`
  - Evento `creating`: autogenera `request_number`
  - Accesor `getTotalAmountAttribute()`: suma `items.total_price` si no hay valor persistido
- `App\Models\PurchaseRequestItem`: Ítems con `product_id/custom_name`, `quantity`, `unit_price`, `total_price`, `required_date`, `status`
- `App\Models\Department`, `Product`, `User`

## Filament (v4 Schema)
- `PurchaseRequestResource`:
  - `form(Schema)`: `Tabs` → Información General, Productos y Servicios (Repeater `items` con cálculos reactivos), Revisión y Envío
  - `infolist(Schema)`: datos clave y formatos
  - `table(Table)`: columnas, filtros, acciones (aprobar, rechazar), exportaciones CSV/PDF (todas y seleccionadas)
  - `getExportableColumns()` centraliza columnas exportables
- `DepartmentResource`: CRUD con exportaciones CSV/PDF y `getExportableColumns()`

## Rutas
- `src/buy/routes/web.php`: `/` (welcome), `POST /logout`
- Rutas de Filament gestionadas por el paquete

## Migraciones
- Usuarios, cache, jobs
- `departments`, `products`, `purchase_requests`, `purchase_request_items`

## Exportaciones
- **CSV**: `fputcsv` con `;` para Excel
- **PDF**: DomPDF con vistas Blade (`exports.purchase-requests.pdf`, `exports.departments.pdf`)

## Integración ERP
- `app/Services/ERPIntegration/`:
  - `DataMapper`: mapeos `local_to_erp` y `erp_to_local` para `purchase_request`, transformaciones de `status` (P/A/R/C)
  - `DynamicMapper`, `PurchaseRequestMapper`: serialización/adaptación de payloads

## Puntos de extensión
- Nuevos campos en PR: migración + fillable/casts + Schema en Resource + tabla/filtros + `getExportableColumns()` + mapeo ERP
- Validaciones: reglas en componentes o FormRequests
- Exportables: vistas PDF basadas en `data_get`

## Observaciones
- Uso consistente de Filament v4 Schema
- DomPDF activo con fuentes compatibles (DejaVu Sans)
- Mapeos ERP desacoplados para extensibilidad
