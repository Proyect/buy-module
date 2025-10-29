<?php

namespace App\Services\ERPIntegration;

class PurchaseRequestMapper extends DataMapper
{
    public function mapToERP(object $localRequest): array
    {
        $mapping = $this->mappings['purchase_request']['local_to_erp'] ?? [];
        $transformations = $this->mappings['purchase_request']['transformations'] ?? [];

        $erpData = [];

        foreach ($mapping as $localField => $erpField) {
            if (!isset($localRequest->{$localField})) {
                continue;
            }

            $value = $localRequest->{$localField};

            if (isset($transformations[$localField]['local_to_erp'])) {
                $value = $this->applyTransformation($value, $transformations[$localField]['local_to_erp']);
            }

            $erpData[$erpField] = $value;
        }

        // Additional ERP-specific fields (safe defaults without Laravel helpers)
        $erpData['empresa_id'] = $erpData['empresa_id'] ?? 1;
        $erpData['created_at'] = $erpData['created_at'] ?? date('Y-m-d H:i:s');
        $erpData['updated_at'] = $erpData['updated_at'] ?? date('Y-m-d H:i:s');

        return $erpData;
    }

    public function mapFromERP(array $erpData): array
    {
        $mapping = $this->mappings['purchase_request']['erp_to_local'] ?? [];
        $transformations = $this->mappings['purchase_request']['transformations'] ?? [];

        $localData = [];

        foreach ($mapping as $erpField => $localField) {
            if (!array_key_exists($erpField, $erpData)) {
                continue;
            }

            $value = $erpData[$erpField];

            if (isset($transformations[$localField]['erp_to_local'])) {
                $value = $this->applyTransformation($value, $transformations[$localField]['erp_to_local']);
            }

            $localData[$localField] = $value;
        }

        return $localData;
    }

    protected function applyTransformation($value, array $map)
    {
        return $map[$value] ?? $value;
    }
}
