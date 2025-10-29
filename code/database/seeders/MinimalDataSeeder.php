<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Department;
use App\Models\PurchaseRequest;

class MinimalDataSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure at least one user exists (or use the currently authenticated in UI)
        $user = User::query()->first();
        if (! $user) {
            $user = User::create([
                'name' => 'Tester',
                'email' => 'tester@example.com',
                'password' => Hash::make('password'),
            ]);
        }

        // Ensure at least one department exists
        $department = Department::query()->first();
        if (! $department) {
            $department = Department::create([
                'name' => 'Centro de Costo 01',
                'code' => 'CC01',
                'description' => 'Departamento de prueba',
                'budget_limit' => 100000,
                'is_active' => true,
            ]);
        }

        // Create a minimal purchase request
        if (! PurchaseRequest::where('request_number', 'PR-0001')->exists()) {
            PurchaseRequest::create([
                'request_number' => 'PR-0001',
                'user_id' => $user->id,
                'department_id' => $department->id,
                'priority' => 'medium',
                'request_date' => now()->toDateString(),
                'required_date' => now()->addDays(7)->toDateString(),
                'justification' => 'Compra de insumos de prueba',
                'notes' => 'Generada por seeder',
                'status' => 'pending',
                'currency' => 'ARS',
            ]);
        }
    }
}
