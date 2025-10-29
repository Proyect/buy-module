<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Datos maestros
        $this->call([
            DepartmentSeeder::class,
            CategorySeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
            MinimalDataSeeder::class,
            RolesAndPermissionsSeeder::class,
            UserRolesDemoSeeder::class,
        ]);
    }
}
