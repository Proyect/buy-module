<?php

namespace App\Services\ERPIntegration;

class DataMapper
{
    protected array $mappings = [];

    public function __construct()
    {
        $this->initializeMappings();
    }

    protected function initializeMappings(): void
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
                        ],
                    ],
                ],
            ],
        ];
    }
}
