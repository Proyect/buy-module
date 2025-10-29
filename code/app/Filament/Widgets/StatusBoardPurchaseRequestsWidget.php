<?php

namespace App\Filament\Widgets;

use App\Models\PurchaseRequest;
use Filament\Widgets\Widget;

class StatusBoardPurchaseRequestsWidget extends Widget
{
    protected string $view = 'filament.widgets.status-board-purchase-requests';

    protected ?string $heading = 'Estado de Solicitudes';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        // Limitar por columna para performance
        $limit = 5;
        $with = ['user:id,name', 'department:id,name'];

        $nuevas = PurchaseRequest::query()
            ->with($with)
            ->where('status', 'pending')
            ->latest('created_at')
            ->limit($limit)
            ->get();

        $enProceso = PurchaseRequest::query()
            ->with($with)
            ->where('status', 'approved')
            ->latest('updated_at')
            ->limit($limit)
            ->get();

        $pendEntrega = PurchaseRequest::query()
            ->with($with)
            ->where('status', 'approved') // puedes ajustar si tienes un estado específico
            ->latest('updated_at')
            ->limit($limit)
            ->get();

        $cerradas = PurchaseRequest::query()
            ->with($with)
            ->where('status', 'completed')
            ->latest('updated_at')
            ->limit($limit)
            ->get();

        return compact('nuevas', 'enProceso', 'pendEntrega', 'cerradas');
    }
}
