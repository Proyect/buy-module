<?php

namespace App\Services\ERPIntegration;

class DynamicMapper
{
    protected array $config;

    public function __construct(?array $config = null)
    {
        // Allow injecting config for tests. If not provided, try to load from Laravel config helper.
        if ($config !== null) {
            $this->config = $config;
        } else {
            // Fallback without Laravel: empty config
            $this->config = function_exists('config') ? (config('erp_mappings') ?? []) : [];
        }
    }

    public function mapEntity(string $entityType, array $data, string $direction = 'local_to_erp'): array
    {
        if (!isset($this->config[$entityType])) {
            throw new \InvalidArgumentException("No mapping found for entity: {$entityType}");
        }

        $mapping = $this->config[$entityType];
        $fields = $mapping['fields'] ?? [];
        $transformations = $mapping['transformations'] ?? [];
        $defaults = $mapping['defaults'] ?? [];

        $result = [];

        foreach ($fields as $sourceField => $targetField) {
            if (!array_key_exists($sourceField, $data)) {
                continue;
            }

            $value = $data[$sourceField];

            if (isset($transformations[$sourceField])) {
                $map = $transformations[$sourceField];
                if ($direction === 'local_to_erp') {
                    $value = $map[$value] ?? $value;
                } else {
                    // inverse transform when available (best-effort)
                    $inverse = array_flip($map);
                    $value = $inverse[$value] ?? $value;
                }
            }

            $result[$targetField] = $value;
        }

        foreach ($defaults as $field => $value) {
            if (!array_key_exists($field, $result)) {
                $result[$field] = $value;
            }
        }

        return $result;
    }
}
