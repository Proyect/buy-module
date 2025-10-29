<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'purchase-requests.viewAny',
            'purchase-requests.view',
            'purchase-requests.create',
            'purchase-requests.update',
            'purchase-requests.delete',
            'purchase-requests.approve',
            'purchase-requests.reject',
            'purchase-requests.export',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        $solicitante = Role::firstOrCreate(['name' => 'solicitante']);
        $solicitante->syncPermissions([
            'purchase-requests.viewAny',
            'purchase-requests.view',
            'purchase-requests.create',
            'purchase-requests.update',
        ]);

        $aprobador = Role::firstOrCreate(['name' => 'aprobador']);
        $aprobador->syncPermissions([
            'purchase-requests.viewAny',
            'purchase-requests.view',
            'purchase-requests.approve',
            'purchase-requests.reject',
        ]);

        $compras = Role::firstOrCreate(['name' => 'compras']);
        $compras->syncPermissions([
            'purchase-requests.viewAny',
            'purchase-requests.view',
            'purchase-requests.update',
            'purchase-requests.export',
        ]);

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions($permissions);
    }
}
