# 📋 Metodología de Desarrollo - Módulos del Sistema de Compras

## 🎯 Objetivo
Crear una metodología estructurada y replicable para implementar cada módulo del sistema de forma consistente.

## 📐 Los 7 Pasos por Módulo

Cada módulo del sistema seguirá estos 7 pasos en orden:

### **Paso 1: Migración de Base de Datos** 🗄️
- Crear archivo de migración
- Definir estructura de tabla con tipos de datos correctos
- Agregar índices para optimización
- Agregar foreign keys y relaciones
- Incluir campos de auditoría (created_at, updated_at)

**Archivo:** `database/migrations/YYYY_MM_DD_HHMMSS_create_[tabla]_table.php`

### **Paso 2: Modelo Eloquent** 🏗️
- Crear clase Model
- Definir `$fillable` o `$guarded`
- Definir `$casts` para conversión de tipos
- Agregar relaciones (belongsTo, hasMany, etc.)
- Agregar scopes y accessors si son necesarios
- Configurar traits (SoftDeletes, LogsActivity, etc.)

**Archivo:** `app/Models/[NombreModelo].php`

### **Paso 3: Factory** 🏭
- Crear Factory para testing y seeders
- Definir datos faker realistas
- Considerar relaciones con otros modelos
- Usar datos en español si aplica

**Archivo:** `database/factories/[NombreModelo]Factory.php`

### **Paso 4: Seeder** 🌱
- Crear Seeder con datos iniciales
- Usar Factory para datos de prueba
- Incluir datos maestros necesarios
- Considerar orden de dependencias

**Archivo:** `database/seeders/[NombreModelo]Seeder.php`

### **Paso 5: Recurso Filament** 🎨
- Crear Resource con comando artisan
- Configurar formulario (Form) con campos apropiados
- Configurar tabla (Table) con columnas, filtros y acciones
- Agregar validaciones
- Configurar navegación y permisos

**Archivos:**
- `app/Filament/Resources/[NombreModelo]Resource.php`
- `app/Filament/Resources/[NombreModelo]Resource/Pages/`

### **Paso 6: Políticas de Acceso (Opcional)** 🔒
- Crear Policy si se requiere lógica de autorización compleja
- Definir métodos: viewAny, view, create, update, delete
- Registrar en AuthServiceProvider

**Archivo:** `app/Policies/[NombreModelo]Policy.php`

### **Paso 7: Testing** ✅
- Crear tests de Feature para CRUD
- Crear tests de Unit para lógica de negocio
- Verificar validaciones
- Verificar permisos si aplica

**Archivos:**
- `tests/Feature/[NombreModelo]Test.php`
- `tests/Unit/[NombreModelo]Test.php`

---

## 🔄 Orden de Implementación de Módulos

### **Fase 1: Catálogos Base** (Sin dependencias)
1. ✅ **Department** (PLANTILLA) - Más simple, sin relaciones complejas
2. **Category** - Similar a Department
3. **CostCenter** - Similar a Department

### **Fase 2: Catálogos con Relaciones**
4. **Supplier** - Depende de categorías opcionales
5. **Product** - Depende de Category y Supplier

### **Fase 3: Usuarios y Permisos**
6. **User** (extender existente) - Agregar campos personalizados
7. **Roles y Permisos** - Implementar Spatie Permission

### **Fase 4: Proceso de Compras**
8. **PurchaseRequest** - Depende de User, Department, Product
9. **PurchaseRequestItem** - Depende de PurchaseRequest, Product
10. **PurchaseOrder** - Depende de PurchaseRequest, Supplier
11. **PurchaseOrderItem** - Depende de PurchaseOrder, Product

### **Fase 5: Inventario**
12. **Inventory** - Depende de Product
13. **StockMovement** - Depende de Product, User

### **Fase 6: Sistema de Aprobaciones**
14. **Approval** - Sistema polymorphic para aprobaciones

### **Fase 7: Integración ERP**
15. **ERPIntegrationLog** - Logging de sincronizaciones
16. **ERPSyncStatus** - Estado de sincronizaciones

---

## 📝 Template de Checklist por Módulo

Copiar y pegar para cada módulo nuevo:

```markdown
## Módulo: [NOMBRE_MODULO]

### Información
- Tabla: `[nombre_tabla]`
- Modelo: `[NombreModelo]`
- Dependencias: [listar modelos relacionados]

### Checklist de Implementación
- [ ] Paso 1: Migración creada y ejecutada
- [ ] Paso 2: Modelo creado con relaciones
- [ ] Paso 3: Factory creado con datos realistas
- [ ] Paso 4: Seeder creado con datos iniciales
- [ ] Paso 5: Recurso Filament creado y funcional
- [ ] Paso 6: Políticas de acceso (si aplica)
- [ ] Paso 7: Tests creados y pasando

### Validación Final
- [ ] Ejecuta sin errores en el panel Filament
- [ ] CRUD completo funciona
- [ ] Relaciones se muestran correctamente
- [ ] Validaciones funcionan
- [ ] Permisos aplicados correctamente
```

---

## 🛠️ Comandos Útiles

### Crear Módulo Completo
```bash
# Paso 1: Migración
php artisan make:migration create_[tabla]_table

# Paso 2: Modelo (con Factory, Seeder, Policy)
php artisan make:model [NombreModelo] -mfsp

# Paso 5: Recurso Filament
php artisan make:filament-resource [NombreModelo] --generate

# Paso 7: Tests
php artisan make:test [NombreModelo]Test
```

### Ejecutar y Verificar
```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders específicos
php artisan db:seed --class=[NombreModelo]Seeder

# Ejecutar tests
php artisan test --filter=[NombreModelo]Test

# Generar permisos Shield
php artisan shield:generate --resource=[NombreModelo]Resource
```

---

## 📊 Convenciones de Código

### Nombres
- **Tablas:** plural, snake_case (`purchase_requests`, `suppliers`)
- **Modelos:** singular, PascalCase (`PurchaseRequest`, `Supplier`)
- **Controladores:** PascalCase + Controller (`PurchaseRequestController`)
- **Variables:** camelCase (`$purchaseRequest`, `$totalAmount`)
- **Constantes:** UPPER_SNAKE_CASE (`STATUS_PENDING`, `MAX_AMOUNT`)

### Estructura de Archivos
```
app/
├── Models/
│   └── [NombreModelo].php
├── Filament/
│   └── Resources/
│       └── [NombreModelo]Resource.php
├── Policies/
│   └── [NombreModelo]Policy.php
├── Services/
│   └── [NombreModelo]Service.php (si aplica)
database/
├── migrations/
│   └── YYYY_MM_DD_HHMMSS_create_[tabla]_table.php
├── factories/
│   └── [NombreModelo]Factory.php
└── seeders/
    └── [NombreModelo]Seeder.php
tests/
├── Feature/
│   └── [NombreModelo]Test.php
└── Unit/
    └── [NombreModelo]UnitTest.php
```

---

## ✅ Criterios de Aceptación

Cada módulo debe cumplir con:

1. **Funcionalidad Completa**
   - CRUD funcional en Filament
   - Validaciones aplicadas
   - Relaciones funcionando

2. **Código Limpio**
   - Sigue PSR-12
   - Comentarios en lógica compleja
   - Nombres descriptivos

3. **Testing**
   - Tests de Feature para CRUD
   - Coverage mínimo del 70%

4. **Documentación**
   - README actualizado
   - Comentarios PHPDoc en métodos públicos

5. **Seguridad**
   - Permisos aplicados
   - Validación de entradas
   - Protección contra SQL injection (Eloquent)

---

## 🚀 Próximos Pasos

1. Implementar **Department** como módulo plantilla
2. Revisar y aprobar implementación
3. Replicar metodología en módulos restantes
4. Documentar variaciones o casos especiales

---

**Versión:** 1.0  
**Fecha:** Octubre 2025  
**Autor:** Sistema de Compras



