<?php

namespace App\Filament\Resources\PurchaseRequests\Pages;

use App\Filament\Resources\PurchaseRequests\PurchaseRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePurchaseRequests extends ManageRecords
{
    protected static string $resource = PurchaseRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nueva solicitud')
                ->icon('heroicon-o-plus')
                ->iconSize('sm')
                ->modalWidth('7xl')
                ->mutateFormDataUsing(function (array $data): array {
                    if (empty($data['status'])) {
                        $data['status'] = 'pending';
                    }
                    if (empty($data['currency'])) {
                        $data['currency'] = 'ARS';
                    }
                    return $data;
                }),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Defaults
        if (empty($data['currency'])) {
            $data['currency'] = 'ARS';
        }

        // Enforce completeness: keep status 'pending' until required fields and at least 1 item exist
        $required = [
            $data['request_number'] ?? null,
            $data['user_id'] ?? null,
            $data['department_id'] ?? null,
            $data['priority'] ?? null,
            $data['request_date'] ?? null,
            $data['required_date'] ?? null,
        ];
        $missingHeader = false;
        foreach ($required as $v) {
            if (empty($v)) { $missingHeader = true; break; }
        }
        $hasItems = isset($data['items']) && is_array($data['items']) && count($data['items']) > 0;

        if ($missingHeader || ! $hasItems) {
            $data['status'] = 'pending';
        } else {
            $data['status'] = $data['status'] ?? 'pending';
        }

        return $data;
    }
}
