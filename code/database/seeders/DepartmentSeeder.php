<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Datos maestros principales (siempre deben existir)
        $masterDepartments = [
            [
                'name' => 'Compras',
                'code' => 'COMP01',
                'description' => 'Departamento encargado de gestionar todas las adquisiciones de la empresa',
                'budget_limit' => 10000000.00, // $10,000,000 ARS
                'is_active' => true,
            ],
            [
                'name' => 'Finanzas',
                'code' => 'FIN01',
                'description' => 'Departamento encargado de la gestión financiera y contable',
                'budget_limit' => 5000000.00, // $5,000,000 ARS
                'is_active' => true,
            ],
            [
                'name' => 'Recursos Humanos',
                'code' => 'RH01',
                'description' => 'Departamento de gestión del talento humano',
                'budget_limit' => 3000000.00, // $3,000,000 ARS
                'is_active' => true,
            ],
            [
                'name' => 'Tecnología',
                'code' => 'TI01',
                'description' => 'Departamento de Tecnologías de la Información',
                'budget_limit' => 7500000.00, // $7,500,000 ARS
                'is_active' => true,
            ],
            [
                'name' => 'Operaciones',
                'code' => 'OPE01',
                'description' => 'Departamento de operaciones y producción',
                'budget_limit' => 15000000.00, // $15,000,000 ARS
                'is_active' => true,
            ],
        ];

        foreach ($masterDepartments as $dept) {
            Department::updateOrCreate(
                ['code' => $dept['code']],
                $dept
            );
        }

        // Datos adicionales para testing (opcional)
        if (app()->environment('local', 'development')) {
            Department::factory(5)->create();
        }
    }
}
