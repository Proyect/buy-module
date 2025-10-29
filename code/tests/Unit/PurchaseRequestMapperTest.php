<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class PurchaseRequestMapperTest extends TestCase
{
    public function test_map_to_erp_transforms_fields_and_values_correctly(): void
    {
        // Arrange: fake local PurchaseRequest-like object
        $localRequest = (object) [
            'id' => 123,
            'user_id' => 456,
            'request_date' => '2024-01-15',
            'total_amount' => 1500.00,
            'status' => 'pending',
            'justification' => 'Necesidad operativa',
            'priority' => 'high',
        ];

        // Act
        $mapper = new \App\Services\ERPIntegration\PurchaseRequestMapper();
        $erpData = $mapper->mapToERP($localRequest);

        // Assert (based on docs/erp-integration-mapping.md examples)
        $this->assertSame(123, $erpData['id']);
        $this->assertSame(456, $erpData['empleado_id']);
        $this->assertSame('2024-01-15', $erpData['fecha_solicitud']);
        $this->assertSame(1500.00, $erpData['monto_total']);
        $this->assertSame('P', $erpData['estado']);
        $this->assertSame('Necesidad operativa', $erpData['justificacion']);
        $this->assertSame('high', $erpData['prioridad']);

        // Extras expected by mapper
        $this->assertArrayHasKey('empresa_id', $erpData);
        $this->assertArrayHasKey('created_at', $erpData);
        $this->assertArrayHasKey('updated_at', $erpData);
    }

    public function test_map_from_erp_transforms_fields_and_values_correctly(): void
    {
        // Arrange
        $erpData = [
            'id' => 123,
            'empleado_id' => 456,
            'fecha_solicitud' => '2024-01-15',
            'monto_total' => 1500.00,
            'estado' => 'A',
            'justificacion' => 'Necesidad operativa',
            'prioridad' => 'high',
        ];

        // Act
        $mapper = new \App\Services\ERPIntegration\PurchaseRequestMapper();
        $localData = $mapper->mapFromERP($erpData);

        // Assert
        $this->assertSame(123, $localData['id']);
        $this->assertSame(456, $localData['user_id']);
        $this->assertSame('2024-01-15', $localData['request_date']);
        $this->assertSame(1500.00, $localData['total_amount']);
        $this->assertSame('approved', $localData['status']);
        $this->assertSame('Necesidad operativa', $localData['justification']);
        $this->assertSame('high', $localData['priority']);
    }
}
