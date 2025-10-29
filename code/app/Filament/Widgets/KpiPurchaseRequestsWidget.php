<?php

namespace App\Filament\Widgets;

use App\Models\PurchaseRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KpiPurchaseRequestsWidget extends BaseWidget
{
    protected ?string $heading = 'Resumen de Solicitudes';

    protected function getStats(): array
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();

        $active = PurchaseRequest::query()->whereIn('status', ['pending','approved'])->count();
        $pendingDelivery = PurchaseRequest::query()->where('status', 'approved')->count();
        $completedThisMonth = PurchaseRequest::query()
            ->where('status', 'completed')
            ->where('updated_at', '>=', $startOfMonth)
            ->count();

        return [
            Stat::make('Mis Solicitudes Activas', number_format($active))
                ->description('Activas (pendientes/aprobadas)')
                ->icon('heroicon-o-shopping-bag')
                ->color('primary'),
            Stat::make('Pendientes de Entrega', number_format($pendingDelivery))
                ->description('Aprobadas en proceso')
                ->icon('heroicon-o-truck')
                ->color('warning'),
            Stat::make('Completadas Este Mes', number_format($completedThisMonth))
                ->description('Desde ' . $startOfMonth->format('d/m'))
                ->icon('heroicon-o-check-badge')
                ->color('success'),
        ];
    }
}
