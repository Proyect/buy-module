<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $rows = [
            ['name' => 'Insumos de Oficina', 'code' => 'OFFICE', 'description' => 'Útiles y papelería', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Equipamiento IT',   'code' => 'IT',     'description' => 'Hardware y periféricos', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mobiliario',        'code' => 'FURN',   'description' => 'Muebles y sillas', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Servicios',         'code' => 'SERV',   'description' => 'Servicios tercerizados', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('categories')->upsert($rows, ['code'], ['name','description','is_active','updated_at']);
    }
}
