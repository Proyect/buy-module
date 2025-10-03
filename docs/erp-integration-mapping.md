# Documentación de Integración ERP - Mapeo de Datos

## Tabla de Contenidos

1. [Introducción](#introducción)
2. [Conceptos Fundamentales](#conceptos-fundamentales)
3. [Tipos de Mapeo](#tipos-de-mapeo)
4. [Implementación Práctica](#implementación-práctica)
5. [Mapeo de Relaciones](#mapeo-de-relaciones)
6. [Validación y Limpieza](#validación-y-limpieza)
7. [Configuración Flexible](#configuración-flexible)
8. [Testing y Monitoreo](#testing-y-monitoreo)
9. [Mejores Prácticas](#mejores-prácticas)
10. [Casos de Uso](#casos-de-uso)

## Introducción

El mapeo de datos es el proceso crítico de transformar información entre nuestro sistema de compras (Laravel/Filament) y el ERP externo. Esta documentación proporciona una guía completa para implementar una integración robusta y mantenible.

### Objetivos del Mapeo

- **Interoperabilidad**: Permitir comunicación entre sistemas diferentes
- **Integridad**: Mantener consistencia de datos entre sistemas
- **Flexibilidad**: Adaptarse a cambios en cualquiera de los sistemas
- **Mantenibilidad**: Facilitar actualizaciones y correcciones

## Conceptos Fundamentales

### Definición de Mapeo de Datos

El mapeo de datos es la correspondencia entre campos de diferentes sistemas, considerando:

- **Nombres de campos** diferentes entre sistemas
- **Tipos de datos** específicos de cada plataforma
- **Formatos** y estructuras particulares
- **Relaciones** entre entidades
- **Validaciones** y reglas de negocio

### Arquitectura de Integración

```
Sistema de Compras (Laravel)    ←→    ERP (Base de Datos Externa)
├── purchase_requests           ←→    ├── orden_compra
├── suppliers                   ←→    ├── proveedores
├── products                    ←→    ├── articulos
├── purchase_orders             ←→    ├── ordenes_compra
├── users                       ←→    ├── empleados
└── inventory                   ←→    └── inventario
```

## Tipos de Mapeo

### 1. Mapeo Directo (1:1)

Correspondencia exacta entre campos:

```php
'purchase_requests' => [
    'id' => 'id',                    // Directo
    'request_date' => 'fecha_solicitud', // Diferente nombre
    'total_amount' => 'monto_total',     // Diferente nombre
    'status' => 'estado',                // Diferente nombre
]
```

### 2. Mapeo con Transformación

Campos que requieren conversión de valores:

```php
'status_mapping' => [
    'pending' => 'P',           // Texto a código
    'approved' => 'A',
    'rejected' => 'R',
    'completed' => 'C',
],

'currency_mapping' => [
    'USD' => 1,                 // Texto a ID numérico
    'EUR' => 2,
    'MXN' => 3,
]
```

### 3. Mapeo Condicional

Mapeo basado en reglas de negocio:

```php
'amount_field' => [
    'condition' => 'if amount > 1000',
    'local_field' => 'requires_approval',
    'erp_field' => 'necesita_autorizacion',
    'value' => true
]
```

## Implementación Práctica

### Clase Mapper Base

```php
<?php

namespace App\Services\ERPIntegration;

class DataMapper
{
    protected $mappings = [];
    
    public function __construct()
    {
        $this->initializeMappings();
    }
    
    protected function initializeMappings()
    {
        $this->mappings = [
            'purchase_request' => [
                'local_to_erp' => [
                    'id' => 'id',
                    'user_id' => 'empleado_id',
                    'request_date' => 'fecha_solicitud',
                    'total_amount' => 'monto_total',
                    'status' => 'estado',
                    'justification' => 'justificacion',
                    'priority' => 'prioridad',
                ],
                'erp_to_local' => [
                    'id' => 'id',
                    'empleado_id' => 'user_id',
                    'fecha_solicitud' => 'request_date',
                    'monto_total' => 'total_amount',
                    'estado' => 'status',
                    'justificacion' => 'justification',
                    'prioridad' => 'priority',
                ],
                'transformations' => [
                    'status' => [
                        'local_to_erp' => [
                            'pending' => 'P',
                            'approved' => 'A',
                            'rejected' => 'R',
                            'completed' => 'C',
                        ],
                        'erp_to_local' => [
                            'P' => 'pending',
                            'A' => 'approved',
                            'R' => 'rejected',
                            'C' => 'completed',
                        ]
                    ]
                ]
            ]
        ];
    }
}
```

### Mapper Específico para Purchase Request

```php
class PurchaseRequestMapper extends DataMapper
{
    public function mapToERP(PurchaseRequest $localRequest): array
    {
        $mapping = $this->mappings['purchase_request']['local_to_erp'];
        $transformations = $this->mappings['purchase_request']['transformations'];
        
        $erpData = [];
        
        foreach ($mapping as $localField => $erpField) {
            $value = $localRequest->{$localField};
            
            // Aplicar transformaciones si existen
            if (isset($transformations[$localField])) {
                $value = $this->applyTransformation(
                    $value, 
                    $transformations[$localField]['local_to_erp']
                );
            }
            
            $erpData[$erpField] = $value;
        }
        
        // Campos adicionales específicos del ERP
        $erpData['empresa_id'] = config('erp.company_id');
        $erpData['created_at'] = now();
        $erpData['updated_at'] = now();
        
        return $erpData;
    }
    
    public function mapFromERP(array $erpData): array
    {
        $mapping = $this->mappings['purchase_request']['erp_to_local'];
        $transformations = $this->mappings['purchase_request']['transformations'];
        
        $localData = [];
        
        foreach ($mapping as $erpField => $localField) {
            if (isset($erpData[$erpField])) {
                $value = $erpData[$erpField];
                
                // Aplicar transformaciones inversas
                if (isset($transformations[$localField])) {
                    $value = $this->applyTransformation(
                        $value,
                        $transformations[$localField]['erp_to_local']
                    );
                }
                
                $localData[$localField] = $value;
            }
        }
        
        return $localData;
    }
    
    protected function applyTransformation($value, array $transformationMap)
    {
        return $transformationMap[$value] ?? $value;
    }
}
```

## Mapeo de Relaciones

### Mapeo de Proveedores

```php
class SupplierMapper extends DataMapper
{
    public function mapToERP(Supplier $localSupplier): array
    {
        return [
            'id' => $localSupplier->id,
            'nombre' => $localSupplier->name,
            'codigo' => $localSupplier->code,
            'rfc' => $localSupplier->tax_id,
            'telefono' => $localSupplier->phone,
            'email' => $localSupplier->email,
            'direccion' => $this->mapAddress($localSupplier->address),
            'estado' => $this->mapSupplierStatus($localSupplier->status),
            'tipo_proveedor' => $this->mapSupplierType($localSupplier->type),
            'moneda_default' => $this->mapCurrency($localSupplier->default_currency),
        ];
    }
    
    private function mapAddress($address): array
    {
        return [
            'calle' => $address['street'],
            'numero' => $address['number'],
            'colonia' => $address['neighborhood'],
            'ciudad' => $address['city'],
            'estado' => $address['state'],
            'codigo_postal' => $address['postal_code'],
            'pais' => $address['country'],
        ];
    }
}
```

### Mapeo de Productos con Categorías

```php
class ProductMapper extends DataMapper
{
    public function mapToERP(Product $localProduct): array
    {
        return [
            'id' => $localProduct->id,
            'codigo' => $localProduct->sku,
            'nombre' => $localProduct->name,
            'descripcion' => $localProduct->description,
            'precio_unitario' => $localProduct->unit_price,
            'moneda' => $this->mapCurrency($localProduct->currency),
            'categoria_id' => $this->mapCategory($localProduct->category),
            'unidad_medida' => $this->mapUnit($localProduct->unit_of_measure),
            'stock_minimo' => $localProduct->min_stock,
            'stock_actual' => $localProduct->current_stock,
            'activo' => $localProduct->is_active ? 1 : 0,
        ];
    }
    
    private function mapCategory($category): int
    {
        // Mapear categorías locales a IDs del ERP
        $categoryMapping = [
            'office_supplies' => 1,
            'it_equipment' => 2,
            'furniture' => 3,
            'services' => 4,
        ];
        
        return $categoryMapping[$category->code] ?? 1;
    }
}
```

### Mapeo de Órdenes de Compra con Items

```php
class PurchaseOrderMapper extends DataMapper
{
    public function mapToERP(PurchaseOrder $localOrder): array
    {
        $erpOrder = [
            'id' => $localOrder->id,
            'numero_orden' => $localOrder->order_number,
            'proveedor_id' => $localOrder->supplier_id,
            'fecha_orden' => $localOrder->order_date->format('Y-m-d'),
            'fecha_entrega_esperada' => $localOrder->expected_delivery->format('Y-m-d'),
            'estado' => $this->mapOrderStatus($localOrder->status),
            'monto_total' => $localOrder->total_amount,
            'moneda' => $this->mapCurrency($localOrder->currency),
            'terminos_pago' => $this->mapPaymentTerms($localOrder->payment_terms),
            'direccion_entrega' => $this->mapDeliveryAddress($localOrder->delivery_address),
            'observaciones' => $localOrder->notes,
        ];
        
        // Mapear items de la orden
        $erpOrder['items'] = $this->mapOrderItems($localOrder->items);
        
        return $erpOrder;
    }
    
    private function mapOrderItems($items): array
    {
        return $items->map(function ($item) {
            return [
                'producto_id' => $item->product_id,
                'cantidad' => $item->quantity,
                'precio_unitario' => $item->unit_price,
                'total' => $item->total_price,
                'descuento' => $item->discount ?? 0,
                'impuestos' => $item->taxes ?? 0,
            ];
        })->toArray();
    }
}
```

## Validación y Limpieza

### Servicio de Validación de Mapeo

```php
class MappingValidationService
{
    public function validateERPData(array $erpData, string $entityType): array
    {
        $errors = [];
        $rules = $this->getValidationRules($entityType);
        
        foreach ($rules as $field => $rule) {
            if (!isset($erpData[$field]) && $rule['required']) {
                $errors[] = "Campo requerido faltante: {$field}";
            }
            
            if (isset($erpData[$field])) {
                $validation = $this->validateField($erpData[$field], $rule);
                if (!$validation['valid']) {
                    $errors[] = "Error en campo {$field}: {$validation['message']}";
                }
            }
        }
        
        return $errors;
    }
    
    private function getValidationRules(string $entityType): array
    {
        return [
            'purchase_order' => [
                'numero_orden' => ['required' => true, 'type' => 'string'],
                'proveedor_id' => ['required' => true, 'type' => 'integer'],
                'monto_total' => ['required' => true, 'type' => 'numeric', 'min' => 0],
                'fecha_orden' => ['required' => true, 'type' => 'date'],
            ],
            'supplier' => [
                'nombre' => ['required' => true, 'type' => 'string'],
                'rfc' => ['required' => true, 'type' => 'string', 'pattern' => '/^[A-Z]{4}[0-9]{6}[A-Z0-9]{3}$/'],
                'email' => ['required' => false, 'type' => 'email'],
            ]
        ];
    }
}
```

## Configuración Flexible

### Archivo de Configuración

```php
// config/erp_mappings.php
return [
    'purchase_request' => [
        'table' => 'orden_compra',
        'fields' => [
            'id' => 'id',
            'user_id' => 'empleado_id',
            'request_date' => 'fecha_solicitud',
            'total_amount' => 'monto_total',
            'status' => 'estado',
        ],
        'transformations' => [
            'status' => [
                'pending' => 'P',
                'approved' => 'A',
                'rejected' => 'R',
            ]
        ],
        'defaults' => [
            'empresa_id' => 1,
            'moneda' => 'MXN',
        ]
    ],
    
    'supplier' => [
        'table' => 'proveedores',
        'fields' => [
            'id' => 'id',
            'name' => 'nombre',
            'code' => 'codigo',
            'tax_id' => 'rfc',
        ],
        'required_fields' => ['nombre', 'rfc'],
        'unique_fields' => ['rfc'],
    ]
];
```

### Mapper Dinámico

```php
class DynamicMapper
{
    protected $config;
    
    public function __construct()
    {
        $this->config = config('erp_mappings');
    }
    
    public function mapEntity($entityType, $data, $direction = 'local_to_erp')
    {
        $mapping = $this->config[$entityType] ?? null;
        
        if (!$mapping) {
            throw new \Exception("No mapping found for entity: {$entityType}");
        }
        
        $fields = $mapping['fields'];
        $transformations = $mapping['transformations'] ?? [];
        $defaults = $mapping['defaults'] ?? [];
        
        $result = [];
        
        // Mapear campos
        foreach ($fields as $sourceField => $targetField) {
            if (isset($data[$sourceField])) {
                $value = $data[$sourceField];
                
                // Aplicar transformaciones
                if (isset($transformations[$sourceField])) {
                    $value = $transformations[$sourceField][$value] ?? $value;
                }
                
                $result[$targetField] = $value;
            }
        }
        
        // Aplicar valores por defecto
        foreach ($defaults as $field => $value) {
            if (!isset($result[$field])) {
                $result[$field] = $value;
            }
        }
        
        return $result;
    }
}
```

## Testing y Monitoreo

### Tests de Mapeo

```php
class MappingTest extends TestCase
{
    public function test_purchase_request_maps_correctly_to_erp()
    {
        $localRequest = PurchaseRequest::factory()->create([
            'status' => 'pending',
            'total_amount' => 1500.00,
            'request_date' => '2024-01-15',
        ]);
        
        $mapper = new PurchaseRequestMapper();
        $erpData = $mapper->mapToERP($localRequest);
        
        $this->assertEquals('P', $erpData['estado']);
        $this->assertEquals(1500.00, $erpData['monto_total']);
        $this->assertEquals('2024-01-15', $erpData['fecha_solicitud']);
    }
    
    public function test_erp_data_maps_correctly_to_local()
    {
        $erpData = [
            'id' => 123,
            'empleado_id' => 456,
            'fecha_solicitud' => '2024-01-15',
            'monto_total' => 1500.00,
            'estado' => 'A',
        ];
        
        $mapper = new PurchaseRequestMapper();
        $localData = $mapper->mapFromERP($erpData);
        
        $this->assertEquals('approved', $localData['status']);
        $this->assertEquals(1500.00, $localData['total_amount']);
        $this->assertEquals(456, $localData['user_id']);
    }
}
```

### Log de Mapeos

```php
class MappingLogger
{
    public function logMapping($entityType, $direction, $originalData, $mappedData, $errors = [])
    {
        Log::channel('erp_mapping')->info('Data mapping executed', [
            'entity_type' => $entityType,
            'direction' => $direction,
            'original_data' => $originalData,
            'mapped_data' => $mappedData,
            'errors' => $errors,
            'timestamp' => now(),
        ]);
    }
}
```

## Mejores Prácticas

### 1. Principios de Diseño

- **Separación de responsabilidades**: Un mapper por entidad
- **Configuración externa**: Mapeos en archivos de configuración
- **Validación robusta**: Validar antes y después del mapeo
- **Logging completo**: Registrar todas las operaciones de mapeo

### 2. Manejo de Errores

```php
class MappingException extends Exception
{
    protected $errors;
    protected $originalData;
    
    public function __construct($message, $errors = [], $originalData = [])
    {
        parent::__construct($message);
        $this->errors = $errors;
        $this->originalData = $originalData;
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
    
    public function getOriginalData()
    {
        return $this->originalData;
    }
}
```

### 3. Performance

- **Caching de mapeos**: Cachear configuraciones de mapeo
- **Batch processing**: Procesar múltiples registros juntos
- **Lazy loading**: Cargar datos relacionados solo cuando se necesiten

### 4. Mantenibilidad

- **Documentación**: Documentar todos los mapeos
- **Versionado**: Mantener versiones de mapeos para rollback
- **Testing**: Cobertura completa de tests

## Casos de Uso

### 1. Sincronización Inicial

```php
class InitialSyncService
{
    public function syncAllMasterData()
    {
        $this->syncSuppliers();
        $this->syncProducts();
        $this->syncCustomers();
        $this->syncDepartments();
    }
    
    private function syncSuppliers()
    {
        $erpSuppliers = ERPSupplier::all();
        
        foreach ($erpSuppliers as $erpSupplier) {
            $mapper = new SupplierMapper();
            $localData = $mapper->mapFromERP($erpSupplier->toArray());
            
            Supplier::updateOrCreate(
                ['erp_id' => $erpSupplier->id],
                $localData
            );
        }
    }
}
```

### 2. Sincronización Bidireccional

```php
class BidirectionalSyncService
{
    public function syncPurchaseOrder($orderId, $direction = 'local_to_erp')
    {
        if ($direction === 'local_to_erp') {
            $localOrder = PurchaseOrder::find($orderId);
            $mapper = new PurchaseOrderMapper();
            $erpData = $mapper->mapToERP($localOrder);
            
            // Crear/actualizar en ERP
            $erpOrderId = $this->createOrUpdateInERP($erpData);
            
            // Actualizar referencia local
            $localOrder->update(['erp_order_id' => $erpOrderId]);
            
        } else {
            // Sincronización desde ERP hacia local
            $erpOrder = $this->getOrderFromERP($orderId);
            $mapper = new PurchaseOrderMapper();
            $localData = $mapper->mapFromERP($erpOrder);
            
            PurchaseOrder::updateOrCreate(
                ['erp_order_id' => $orderId],
                $localData
            );
        }
    }
}
```

### 3. Resolución de Conflictos

```php
class ConflictResolutionService
{
    public function resolveConflict($entityType, $localData, $erpData)
    {
        $strategy = $this->getConflictResolutionStrategy($entityType);
        
        switch ($strategy) {
            case 'erp_priority':
                return $this->prioritizeERP($localData, $erpData);
                
            case 'local_priority':
                return $this->prioritizeLocal($localData, $erpData);
                
            case 'merge':
                return $this->mergeData($localData, $erpData);
                
            case 'manual':
                return $this->flagForManualResolution($entityType, $localData, $erpData);
        }
    }
    
    private function getConflictResolutionStrategy($entityType)
    {
        $strategies = [
            'supplier' => 'erp_priority',    // ERP es fuente de verdad para proveedores
            'product' => 'erp_priority',     // ERP es fuente de verdad para productos
            'purchase_order' => 'local_priority', // Sistema local maneja órdenes
            'user' => 'merge',               // Combinar datos de ambos sistemas
        ];
        
        return $strategies[$entityType] ?? 'manual';
    }
}
```

## Conclusión

El mapeo de datos es fundamental para una integración exitosa entre sistemas. Siguiendo estas prácticas y patrones, puedes crear una integración robusta, mantenible y escalable que permita la comunicación efectiva entre tu sistema de compras y el ERP externo.

### Próximos Pasos

1. Implementar los mappers básicos
2. Configurar la validación de datos
3. Establecer el sistema de logging
4. Crear tests comprehensivos
5. Implementar la sincronización bidireccional
6. Configurar el monitoreo y alertas

---

**Versión**: 1.0  
**Fecha**: Enero 2024  
**Autor**: Sistema de Compras - Documentación Técnica
