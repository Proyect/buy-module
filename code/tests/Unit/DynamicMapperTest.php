<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class DynamicMapperTest extends TestCase
{
    public function test_dynamic_mapper_maps_fields_transformations_and_defaults(): void
    {
        // Given a config mapping like in docs/erp-integration-mapping.md
        $config = [
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
                    ],
                ],
                'defaults' => [
                    'empresa_id' => 1,
                    'moneda' => 'MXN',
                ],
            ],
        ];

        $data = [
            'id' => 10,
            'user_id' => 20,
            'request_date' => '2024-01-15',
            'total_amount' => 999.50,
            'status' => 'pending',
        ];

        // When
        $mapper = new \App\Services\ERPIntegration\DynamicMapper($config);
        $mapped = $mapper->mapEntity('purchase_request', $data, 'local_to_erp');

        // Then
        $this->assertSame(10, $mapped['id']);
        $this->assertSame(20, $mapped['empleado_id']);
        $this->assertSame('2024-01-15', $mapped['fecha_solicitud']);
        $this->assertSame(999.50, $mapped['monto_total']);
        $this->assertSame('P', $mapped['estado']);
        $this->assertSame(1, $mapped['empresa_id']);
        $this->assertSame('MXN', $mapped['moneda']);
    }
}
