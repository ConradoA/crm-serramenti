<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'view_clients',
            'create_clients',
            'edit_clients',
            'delete_clients',
            'view_suppliers',
            'create_suppliers',
            'edit_suppliers',
            'delete_suppliers',
            'view_materials',
            'create_materials',
            'edit_materials',
            'delete_materials',
            'view_estimates',
            'create_estimates',
            'edit_estimates',
            'delete_estimates',
            'view_orders',
            'create_orders',
            'edit_orders',
            'delete_orders',
            'view_invoices',
            'create_invoices',
            'edit_invoices',
            'delete_invoices',
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Admin Role and assign all permissions
        $role1 = Role::firstOrCreate(['name' => 'Super Admin']);
        // Super Admin gets all permissions via Gate::before rule usually, but for now we can rely on FilamentShield or just assigning them if needed. 
        // For simplicity, we won't assign specific permissions to Super Admin if we handle it in AuthServiceProvider, 
        // but explicit assignment is safer for beginners.
        $role1->givePermissionTo(Permission::all());

        // Create Staff Role
        $role2 = Role::firstOrCreate(['name' => 'Staff']);
        $role2->givePermissionTo([
            'view_clients',
            'create_clients',
            'edit_clients',
            'view_estimates',
            'create_estimates',
            'edit_estimates',
            'view_materials',
            'view_products',
        ]);
    }
}
