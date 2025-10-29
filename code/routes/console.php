<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('purchase:reports', function () {
    $this->info('=== Purchase Module Reports ===');

    // Basic counts
    $counts = [
        'users' => DB::table('users')->count(),
        'departments' => DB::table('departments')->count(),
        'categories' => DB::table('categories')->count(),
        'suppliers' => DB::table('suppliers')->count(),
        'products' => DB::table('products')->count(),
        'purchase_requests' => DB::table('purchase_requests')->count(),
        'purchase_orders' => DB::table('purchase_orders')->count(),
        'stock_movements' => DB::table('stock_movements')->count(),
    ];
    $this->line('Counts:');
    foreach ($counts as $k => $v) {
        $this->line("  - {$k}: {$v}");
    }

    // Pending approvals (purchase requests)
    $pendingPR = DB::table('purchase_requests')->where('status', 'pending')->count();
    $this->line("Pending purchase requests: {$pendingPR}");

    // Backlog by supplier (PO not delivered)
    $backlog = DB::table('purchase_orders')->whereIn('status', ['sent','confirmed','in_transit'])->count();
    $this->line("PO backlog (not delivered): {$backlog}");

    // Low stock products (quantity_available approximated as current_stock - 0)
    $lowStock = DB::table('products')->whereColumn('current_stock', '<', 'min_stock')->count();
    $this->line("Low stock products: {$lowStock}");

    // Last 5 stock movements
    $this->line("Last 5 stock movements:");
    $movs = DB::table('stock_movements')->orderByDesc('movement_date')->limit(5)->get();
    foreach ($movs as $m) {
        $this->line(sprintf(
            '  - [%s] product_id=%d type=%s qty=%d total=%s',
            $m->movement_date,
            $m->product_id,
            $m->movement_type,
            $m->quantity,
            $m->total_cost
        ));
    }
})->purpose('Show purchase module checks and sample queries');
