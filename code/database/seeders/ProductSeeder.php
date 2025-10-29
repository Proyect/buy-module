<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Obtener IDs de categorías y proveedores por code para relacionar
        $categories = DB::table('categories')->pluck('id', 'code');
        $suppliers  = DB::table('suppliers')->pluck('id', 'code');

        $rows = [
            [
                'sku' => 'OFF-001',
                'name' => 'Resma A4 80gr',
                'description' => 'Papel tamaño A4 80 gramos',
                'category_id' => $categories['OFFICE'] ?? null,
                'supplier_id' => $suppliers['SUP-ALFA'] ?? null,
                'unit_price' => 4500.00,
                'currency' => 'ARS',
                'unit_of_measure' => 'pack',
                'min_stock' => 10,
                'max_stock' => 200,
                'current_stock' => 50,
                'lead_time' => 5,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'sku' => 'IT-KEY-USB',
                'name' => 'Teclado USB',
                'description' => 'Teclado estándar USB',
                'category_id' => $categories['IT'] ?? null,
                'supplier_id' => $suppliers['SUP-BETA'] ?? null,
                'unit_price' => 12000.00,
                'currency' => 'ARS',
                'unit_of_measure' => 'pcs',
                'min_stock' => 5,
                'max_stock' => 100,
                'current_stock' => 20,
                'lead_time' => 7,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('products')->upsert($rows, ['sku'], [
            'name','description','category_id','supplier_id','unit_price','currency','unit_of_measure','min_stock','max_stock','current_stock','lead_time','is_active','updated_at'
        ]);
    }
}
