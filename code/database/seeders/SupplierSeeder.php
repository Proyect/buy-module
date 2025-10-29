<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $rows = [
            [
                'name' => 'Proveedor Alfa',
                'code' => 'SUP-ALFA',
                'tax_id' => 'ALFA123456789',
                'contact_name' => 'María Pérez',
                'contact_email' => 'contacto@alfa.com',
                'contact_phone' => '+54 11 4000-0001',
                'address' => json_encode([
                    'street' => 'Av. Siempre Viva',
                    'number' => '742',
                    'city' => 'Buenos Aires',
                    'state' => 'CABA',
                    'postal_code' => '1000',
                    'country' => 'AR',
                ]),
                'payment_terms' => 30,
                'currency' => 'ARS',
                'rating' => 4.5,
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Tecno Beta',
                'code' => 'SUP-BETA',
                'tax_id' => 'BETA123456789',
                'contact_name' => 'Juan Gómez',
                'contact_email' => 'ventas@tecnobeta.com',
                'contact_phone' => '+54 11 4000-0002',
                'address' => json_encode([
                    'street' => 'Calle 9 de Julio',
                    'number' => '100',
                    'city' => 'Córdoba',
                    'state' => 'Córdoba',
                    'postal_code' => '5000',
                    'country' => 'AR',
                ]),
                'payment_terms' => 45,
                'currency' => 'USD',
                'rating' => 4.2,
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('suppliers')->upsert($rows, ['code'], [
            'name','tax_id','contact_name','contact_email','contact_phone','address','payment_terms','currency','rating','status','updated_at'
        ]);
    }
}
