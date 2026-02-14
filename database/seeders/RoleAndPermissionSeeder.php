<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view dashboard',
            'access warehouse',
            'access catalogue',
            'create pr',
            'access goods receipt',
            'negotiation',
            'manage company',
            'manage users',
            'manage roles',
            'approve pr',
            'approve po',
            'approve do',
            'approve invoice',
            'approve goods return',
            'approve debit notes',
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign common permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions([
            'view dashboard',
            'manage company',
            'manage users',
            'approve pr',
            'approve po',
            'approve do',
            'approve invoice',
            'approve goods return',
            'approve debit notes',
            'create pr',
            'access warehouse',
            'access catalogue',
            'access goods receipt',
            'negotiation',
        ]);

        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->syncPermissions([
            'view dashboard',
            'approve pr',
            'approve po',
            'approve do',
            'approve goods return',
            'approve debit notes',
            'manage roles',
            'manage users',
            'negotiation',
        ]);

        $purchasingManager = Role::firstOrCreate(['name' => 'purchasing_manager']);
        $purchasingManager->syncPermissions([
            'view dashboard',
            'approve pr',
            'approve po',
            'approve do',
            'approve invoice',
            'create pr',
            'negotiation',
        ]);

        $finance = Role::firstOrCreate(['name' => 'finance']);
        $finance->syncPermissions([
            'view dashboard',
            'approve invoice',
            'view reports',
        ]);

        $staff = Role::firstOrCreate(['name' => 'staff']);
        $staff->syncPermissions([
            'view dashboard',
            'access warehouse',
            'access catalogue',
            'create pr',
            'access goods receipt',
            'negotiation',
        ]);

        // Keep legacy company_admin for backward compatibility
        $companyAdmin = Role::firstOrCreate(['name' => 'company_admin']);
        $companyAdmin->syncPermissions(Permission::all());
    }
}
