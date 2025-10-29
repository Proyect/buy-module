<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public static function getNavigationLabel(): string
    {
        return 'Panel Principal';
    }

    public function getColumns(): int|array
    {
        // Grid de widgets: 1 columna en móvil, 3 en escritorio
        return [
            'sm' => 1,
            'lg' => 3,
        ];
    }

    public function getWidgets(): array
    {
        // Conservamos los mismos widgets/datasets, solo ordenamos la disposición
        return [
            \App\Filament\Widgets\KpiPurchaseRequestsWidget::class,
            \App\Filament\Widgets\StatusBoardPurchaseRequestsWidget::class,
            \Filament\Widgets\AccountWidget::class,
            \Filament\Widgets\FilamentInfoWidget::class,
        ];
    }
}
