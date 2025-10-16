# Modelo de Base de Datos - Sistema de Compras

## Tabla de Contenidos

1. [Introducción](#introducción)
2. [Arquitectura de Base de Datos](#arquitectura-de-base-de-datos)
3. [Modelos Principales](#modelos-principales)
4. [Relaciones](#relaciones)
5. [Migraciones](#migraciones)
6. [Seeders](#seeders)
7. [Índices y Optimización](#índices-y-optimización)
8. [Integración ERP](#integración-erp)

## Introducción

Este documento describe el modelo de base de datos completo para el sistema de compras integrado con ERP. La estructura está diseñada para soportar todo el flujo de trabajo de compras, desde la solicitud hasta la recepción de mercancías.

## Arquitectura de Base de Datos

### Diagrama de Entidad-Relación

```
Users ←→ PurchaseRequests ←→ PurchaseRequestItems ←→ Products
  ↓           ↓                        ↓              ↓
Roles ←→ Approvals ←→ PurchaseOrders ←→ PurchaseOrderItems
  ↓           ↓              ↓              ↓
Permissions   ↓         Suppliers ←→ Inventory
              ↓
        AuditLogs
```

### Esquema General

```sql
-- Sistema de Usuarios y Permisos
users
roles
permissions
model_has_roles
model_has_permissions

-- Catálogo Maestro
suppliers
products
categories
departments
cost_centers

-- Proceso de Compras
purchase_requests
purchase_request_items
purchase_orders
purchase_order_items
approvals

-- Inventario
inventory
stock_movements
inventory_transactions

-- Integración ERP
erp_integration_logs
erp_sync_status

-- Auditoría
audit_logs
activity_logs
```

## Modelos Principales

### 1. Sistema de Usuarios y Roles

#### Tabla: users
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    employee_id VARCHAR(50) UNIQUE NULL,
    department_id BIGINT UNSIGNED NULL,
    position VARCHAR(100) NULL,
    phone VARCHAR(20) NULL,
    manager_id BIGINT UNSIGNED NULL,
    approval_limit DECIMAL(15,2) DEFAULT 0.00,
    is_active BOOLEAN DEFAULT TRUE,
    erp_user_id BIGINT UNSIGNED NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_users_department (department_id),
    INDEX idx_users_manager (manager_id),
    INDEX idx_users_erp (erp_user_id),
    INDEX idx_users_active (is_active),
    
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (manager_id) REFERENCES users(id)
);
```

#### Tabla: roles
```sql
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    level INT DEFAULT 1,
    approval_limit DECIMAL(15,2) DEFAULT 0.00,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    UNIQUE KEY unique_role_name (name, guard_name),
    INDEX idx_roles_level (level)
);
```

#### Tabla: permissions
```sql
CREATE TABLE permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    category VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    UNIQUE KEY unique_permission_name (name, guard_name),
    INDEX idx_permissions_category (category)
);
```

### 2. Catálogo Maestro

#### Tabla: departments
```sql
CREATE TABLE departments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    description TEXT NULL,
    manager_id BIGINT UNSIGNED NULL,
    cost_center_id BIGINT UNSIGNED NULL,
    budget_limit DECIMAL(15,2) DEFAULT 0.00,
    is_active BOOLEAN DEFAULT TRUE,
    erp_department_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_departments_manager (manager_id),
    INDEX idx_departments_cost_center (cost_center_id),
    INDEX idx_departments_erp (erp_department_id),
    
    FOREIGN KEY (manager_id) REFERENCES users(id),
    FOREIGN KEY (cost_center_id) REFERENCES cost_centers(id)
);
```

#### Tabla: suppliers  //proveedores
```sql
CREATE TABLE suppliers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    tax_id VARCHAR(20) UNIQUE NULL,
    contact_name VARCHAR(255) NULL,
    contact_email VARCHAR(255) NULL,
    contact_phone VARCHAR(20) NULL,
    address JSON NULL,
    payment_terms INT DEFAULT 30,
    currency VARCHAR(3) DEFAULT 'MXN',
    rating DECIMAL(3,2) DEFAULT 0.00,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    erp_supplier_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_suppliers_code (code),
    INDEX idx_suppliers_tax_id (tax_id),
    INDEX idx_suppliers_status (status),
    INDEX idx_suppliers_erp (erp_supplier_id)
);
```

#### Tabla: products
```sql
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    category_id BIGINT UNSIGNED NULL,
    supplier_id BIGINT UNSIGNED NULL,
    unit_price DECIMAL(15,2) DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'MXN',
    unit_of_measure VARCHAR(50) DEFAULT 'pcs',
    min_stock INT DEFAULT 0,
    max_stock INT DEFAULT 0,
    current_stock INT DEFAULT 0,
    lead_time INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    erp_product_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_products_sku (sku),
    INDEX idx_products_category (category_id),
    INDEX idx_products_supplier (supplier_id),
    INDEX idx_products_active (is_active),
    INDEX idx_products_erp (erp_product_id),
    
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);
```

#### Tabla: categories
```sql
CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    description TEXT NULL,
    parent_id BIGINT UNSIGNED NULL,
    is_active BOOLEAN DEFAULT TRUE,
    erp_category_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_categories_code (code),
    INDEX idx_categories_parent (parent_id),
    INDEX idx_categories_active (is_active),
    
    FOREIGN KEY (parent_id) REFERENCES categories(id)
);
```

### 3. Proceso de Compras

#### Tabla: purchase_requests //solicitudes de compra
```sql
CREATE TABLE purchase_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_number VARCHAR(50) UNIQUE NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    department_id BIGINT UNSIGNED NOT NULL,
    request_date DATE NOT NULL,
    required_date DATE NULL,
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    status ENUM('draft', 'pending', 'approved', 'rejected', 'completed', 'cancelled') DEFAULT 'draft',
    total_amount DECIMAL(15,2) DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'MXN',
    justification TEXT NOT NULL,
    notes TEXT NULL,
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    rejected_by BIGINT UNSIGNED NULL,
    rejected_at TIMESTAMP NULL,
    rejection_reason TEXT NULL,
    erp_request_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_purchase_requests_user (user_id),
    INDEX idx_purchase_requests_department (department_id),
    INDEX idx_purchase_requests_status (status),
    INDEX idx_purchase_requests_date (request_date),
    INDEX idx_purchase_requests_number (request_number),
    INDEX idx_purchase_requests_erp (erp_request_id),
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    FOREIGN KEY (rejected_by) REFERENCES users(id)
);
```

#### Tabla: purchase_request_items //items de las solicitudes de compra
```sql
CREATE TABLE purchase_request_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    purchase_request_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(15,2) DEFAULT 0.00,
    total_price DECIMAL(15,2) DEFAULT 0.00,
    description TEXT NULL,
    specifications TEXT NULL,
    required_date DATE NULL,
    status ENUM('pending', 'quoted', 'ordered', 'received', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_purchase_request_items_request (purchase_request_id),
    INDEX idx_purchase_request_items_product (product_id),
    INDEX idx_purchase_request_items_status (status),
    
    FOREIGN KEY (purchase_request_id) REFERENCES purchase_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

#### Tabla: purchase_orders //ordenes de compra
```sql
CREATE TABLE purchase_orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    purchase_request_id BIGINT UNSIGNED NULL,
    supplier_id BIGINT UNSIGNED NOT NULL,
    order_date DATE NOT NULL,
    expected_delivery DATE NULL,
    actual_delivery DATE NULL,
    status ENUM('draft', 'sent', 'confirmed', 'in_transit', 'delivered', 'cancelled') DEFAULT 'draft',
    subtotal DECIMAL(15,2) DEFAULT 0.00,
    tax_amount DECIMAL(15,2) DEFAULT 0.00,
    discount_amount DECIMAL(15,2) DEFAULT 0.00,
    shipping_cost DECIMAL(15,2) DEFAULT 0.00,
    total_amount DECIMAL(15,2) DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'MXN',
    payment_terms INT DEFAULT 30,
    shipping_address JSON NULL,
    billing_address JSON NULL,
    notes TEXT NULL,
    erp_order_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_purchase_orders_request (purchase_request_id),
    INDEX idx_purchase_orders_supplier (supplier_id),
    INDEX idx_purchase_orders_status (status),
    INDEX idx_purchase_orders_date (order_date),
    INDEX idx_purchase_orders_number (order_number),
    INDEX idx_purchase_orders_erp (erp_order_id),
    
    FOREIGN KEY (purchase_request_id) REFERENCES purchase_requests(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);
```

#### Tabla: purchase_order_items //items de las ordenes de compra
```sql
CREATE TABLE purchase_order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    purchase_order_id BIGINT UNSIGNED NOT NULL,
    purchase_request_item_id BIGINT UNSIGNED NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(15,2) NOT NULL,
    discount_percentage DECIMAL(5,2) DEFAULT 0.00,
    tax_percentage DECIMAL(5,2) DEFAULT 0.00,
    total_price DECIMAL(15,2) NOT NULL,
    received_quantity INT DEFAULT 0,
    pending_quantity INT NOT NULL,
    status ENUM('pending', 'partial', 'received', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_purchase_order_items_order (purchase_order_id),
    INDEX idx_purchase_order_items_request_item (purchase_request_item_id),
    INDEX idx_purchase_order_items_product (product_id),
    INDEX idx_purchase_order_items_status (status),
    
    FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (purchase_request_item_id) REFERENCES purchase_request_items(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

### 4. Sistema de Aprobaciones

#### Tabla: approvals //aprobaciones
```sql
CREATE TABLE approvals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    approvable_type VARCHAR(255) NOT NULL,
    approvable_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NULL,
    approval_level INT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    comments TEXT NULL,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_approvals_approvable (approvable_type, approvable_id),
    INDEX idx_approvals_user (user_id),
    INDEX idx_approvals_role (role_id),
    INDEX idx_approvals_status (status),
    INDEX idx_approvals_level (approval_level),
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (role_id) REFERENCES roles(id)
);
```

### 5. Inventario

#### Tabla: inventory //inventario
```sql
CREATE TABLE inventory (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    warehouse_location VARCHAR(100) NULL,
    quantity_on_hand INT DEFAULT 0,
    quantity_reserved INT DEFAULT 0,
    quantity_available INT GENERATED ALWAYS AS (quantity_on_hand - quantity_reserved) STORED,
    reorder_point INT DEFAULT 0,
    max_quantity INT DEFAULT 0,
    unit_cost DECIMAL(15,2) DEFAULT 0.00,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    UNIQUE KEY unique_product_location (product_id, warehouse_location),
    INDEX idx_inventory_product (product_id),
    INDEX idx_inventory_available (quantity_available),
    INDEX idx_inventory_reorder (reorder_point),
    
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

#### Tabla: stock_movements //movimientos de stock
```sql
CREATE TABLE stock_movements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    movement_type ENUM('in', 'out', 'adjustment', 'transfer') NOT NULL,
    quantity INT NOT NULL,
    unit_cost DECIMAL(15,2) DEFAULT 0.00,
    total_cost DECIMAL(15,2) DEFAULT 0.00,
    reference_type VARCHAR(255) NULL,
    reference_id BIGINT UNSIGNED NULL,
    warehouse_location VARCHAR(100) NULL,
    notes TEXT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    movement_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_stock_movements_product (product_id),
    INDEX idx_stock_movements_type (movement_type),
    INDEX idx_stock_movements_reference (reference_type, reference_id),
    INDEX idx_stock_movements_date (movement_date),
    INDEX idx_stock_movements_user (user_id),
    
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### 6. Integración ERP

#### Tabla: erp_integration_logs //logs de integración ERP
```sql
CREATE TABLE erp_integration_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    operation_type VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100) NOT NULL,
    local_record_id BIGINT UNSIGNED NULL,
    erp_record_id BIGINT UNSIGNED NULL,
    direction ENUM('local_to_erp', 'erp_to_local') NOT NULL,
    status ENUM('pending', 'success', 'failed', 'retry') DEFAULT 'pending',
    request_data JSON NULL,
    response_data JSON NULL,
    error_message TEXT NULL,
    retry_count INT DEFAULT 0,
    max_retries INT DEFAULT 3,
    processed_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_erp_logs_operation (operation_type),
    INDEX idx_erp_logs_entity (entity_type),
    INDEX idx_erp_logs_status (status),
    INDEX idx_erp_logs_direction (direction),
    INDEX idx_erp_logs_processed (processed_at)
);
```

#### Tabla: erp_sync_status
```sql
CREATE TABLE erp_sync_status (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    entity_type VARCHAR(100) NOT NULL,
    last_sync_at TIMESTAMP NULL,
    sync_status ENUM('success', 'failed', 'in_progress') DEFAULT 'success',
    records_synced INT DEFAULT 0,
    error_count INT DEFAULT 0,
    next_sync_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    UNIQUE KEY unique_entity_type (entity_type),
    INDEX idx_erp_sync_last_sync (last_sync_at),
    INDEX idx_erp_sync_status (sync_status)
);
```

### 7. Auditoría

#### Tabla: audit_logs
```sql
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    auditable_type VARCHAR(255) NOT NULL,
    auditable_id BIGINT UNSIGNED NOT NULL,
    event VARCHAR(255) NOT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    url TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP NULL,
    
    INDEX idx_audit_logs_auditable (auditable_type, auditable_id),
    INDEX idx_audit_logs_user (user_id),
    INDEX idx_audit_logs_event (event),
    INDEX idx_audit_logs_created (created_at),
    
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## Relaciones

### Relaciones Principales

```php
// User Model
class User extends Model
{
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
    
    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }
}

// PurchaseRequest Model
class PurchaseRequest extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    public function items()
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }
    
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
    
    public function approvals()
    {
        return $this->morphMany(Approval::class, 'approvable');
    }
}

// PurchaseOrder Model
class PurchaseOrder extends Model
{
    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }
    
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}
```

## Migraciones

### Migración Principal
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Crear tablas en orden de dependencias
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->text('description')->nullable();
            $table->integer('level')->default(1);
            $table->decimal('approval_limit', 15, 2)->default(0.00);
            $table->timestamps();
            
            $table->unique(['name', 'guard_name']);
            $table->index('level');
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->timestamps();
            
            $table->unique(['name', 'guard_name']);
            $table->index('category');
        });

        // Continuar con el resto de las tablas...
    }

    public function down()
    {
        // Eliminar en orden inverso
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('erp_sync_status');
        Schema::dropIfExists('erp_integration_logs');
        // ... resto de tablas
    }
};
```

## Seeders

### Seeder Principal
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RolePermissionSeeder::class,
            DepartmentSeeder::class,
            CategorySeeder::class,
            UserSeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
        ]);
    }
}

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
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
            
            // Report Permissions
            'view-reports',
            'export-reports',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles
        $roles = [
            'solicitante' => [
                'create-purchase-requests',
                'view-purchase-requests',
                'edit-purchase-requests',
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
            ],
            'supervisor' => [
                'approve-purchase-requests',
                'view-reports',
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

## Índices y Optimización

### Índices Recomendados

```sql
-- Índices compuestos para consultas frecuentes
CREATE INDEX idx_purchase_requests_status_date ON purchase_requests(status, request_date);
CREATE INDEX idx_purchase_orders_status_date ON purchase_orders(status, order_date);
CREATE INDEX idx_stock_movements_product_date ON stock_movements(product_id, movement_date);

-- Índices para integración ERP
CREATE INDEX idx_users_erp_active ON users(erp_user_id, is_active);
CREATE INDEX idx_suppliers_erp_active ON suppliers(erp_supplier_id, status);
CREATE INDEX idx_products_erp_active ON products(erp_product_id, is_active);

-- Índices para auditoría
CREATE INDEX idx_audit_logs_auditable_created ON audit_logs(auditable_type, auditable_id, created_at);
CREATE INDEX idx_erp_logs_status_created ON erp_integration_logs(status, created_at);
```

## Integración ERP

### Campos de Integración

Todas las tablas principales incluyen campos para integración con ERP:

- `erp_[entity]_id`: ID del registro en el ERP
- Campos de sincronización en `erp_integration_logs`
- Estado de sincronización en `erp_sync_status`

### Estrategia de Sincronización

1. **Datos Maestros**: Sincronización unidireccional desde ERP
2. **Datos Transaccionales**: Sincronización bidireccional
3. **Auditoría**: Log completo de todas las operaciones

---

**Versión**: 1.0  
**Fecha**: Enero 2024  
**Autor**: Sistema de Compras - Documentación de Base de Datos
