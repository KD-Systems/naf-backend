<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            ['guard_name' => 'sanctum', 'name' => 'Admin'],
            ['guard_name' => 'sanctum', 'name' => 'Employee']
        ];

        $modules = [
            'Employees' => [
                ['name' => 'employees_access', 'guard_name' => 'sanctum'],
                ['name' => 'employees_create', 'guard_name' => 'sanctum'],
                ['name' => 'employees_show', 'guard_name' => 'sanctum'],
                ['name' => 'employees_edit', 'guard_name' => 'sanctum'],
                ['name' => 'employees_delete', 'guard_name' => 'sanctum'],
            ],
            'Designations' => [
                ['name' => 'designations_access', 'guard_name' => 'sanctum'],
                ['name' => 'designations_create', 'guard_name' => 'sanctum'],
                ['name' => 'designations_show', 'guard_name' => 'sanctum'],
                ['name' => 'designations_edit', 'guard_name' => 'sanctum'],
                ['name' => 'designations_delete', 'guard_name' => 'sanctum'],
            ],

            'Roles' => [
                ['name' => 'roles_access', 'guard_name' => 'sanctum'],
                ['name' => 'roles_create', 'guard_name' => 'sanctum'],
                ['name' => 'roles_show', 'guard_name' => 'sanctum'],
                ['name' => 'roles_edit', 'guard_name' => 'sanctum'],
                ['name' => 'roles_delete', 'guard_name' => 'sanctum'],
            ],

            'Companies' => [
                ['name' => 'companies_access', 'guard_name' => 'sanctum'],
                ['name' => 'companies_create', 'guard_name' => 'sanctum'],
                ['name' => 'companies_show', 'guard_name' => 'sanctum'],
                ['name' => 'companies_edit', 'guard_name' => 'sanctum'],
                ['name' => 'companies_delete', 'guard_name' => 'sanctum'],
            ],

            'Contracts' => [
                ['name' => 'contracts_access', 'guard_name' => 'sanctum'],
                ['name' => 'contracts_create', 'guard_name' => 'sanctum'],
                ['name' => 'contracts_show', 'guard_name' => 'sanctum'],
                ['name' => 'contracts_edit', 'guard_name' => 'sanctum'],
                ['name' => 'contracts_delete', 'guard_name' => 'sanctum'],
            ],
            'Warehouses' => [

                ['name' => 'warehouses_access', 'guard_name' => 'sanctum'],
                ['name' => 'warehouses_create', 'guard_name' => 'sanctum'],
                ['name' => 'warehouses_show', 'guard_name' => 'sanctum'],
                ['name' => 'warehouses_edit', 'guard_name' => 'sanctum'],
                ['name' => 'warehouses_delete', 'guard_name' => 'sanctum'],
            ],
            'Machines' => [
                ['name' => 'machines_access', 'guard_name' => 'sanctum'],
                ['name' => 'machines_create', 'guard_name' => 'sanctum'],
                ['name' => 'machines_show', 'guard_name' => 'sanctum'],
                ['name' => 'machines_edit', 'guard_name' => 'sanctum'],
                ['name' => 'machines_delete', 'guard_name' => 'sanctum'],
            ],
            'Parts' => [
                ['name' => 'parts_access', 'guard_name' => 'sanctum'],
                ['name' => 'parts_create', 'guard_name' => 'sanctum'],
                ['name' => 'parts_show', 'guard_name' => 'sanctum'],
                ['name' => 'parts_edit', 'guard_name' => 'sanctum'],
                ['name' => 'parts_delete', 'guard_name' => 'sanctum'],
            ],
            'Requisitions' => [

                ['name' => 'requisitions_access', 'guard_name' => 'sanctum'],
                ['name' => 'requisitions_create', 'guard_name' => 'sanctum'],
                ['name' => 'requisitions_show', 'guard_name' => 'sanctum'],
                ['name' => 'requisitions_edit', 'guard_name' => 'sanctum'],
                ['name' => 'requisitions_delete', 'guard_name' => 'sanctum'],
            ],
            'Quotations' => [
                ['name' => 'quotations_access', 'guard_name' => 'sanctum'],
                ['name' => 'quotations_create', 'guard_name' => 'sanctum'],
                ['name' => 'quotations_show', 'guard_name' => 'sanctum'],
                ['name' => 'quotations_edit', 'guard_name' => 'sanctum'],
                ['name' => 'quotations_delete', 'guard_name' => 'sanctum'],
            ],

            'Invoices' => [
                ['name' => 'invoices_access', 'guard_name' => 'sanctum'],
                ['name' => 'invoices_create', 'guard_name' => 'sanctum'],
                ['name' => 'invoices_show', 'guard_name' => 'sanctum'],
                ['name' => 'invoices_edit', 'guard_name' => 'sanctum'],
                ['name' => 'invoices_delete', 'guard_name' => 'sanctum'],
            ],
            'Delivery Notes' => [
                ['name' => 'deliverynotes_access', 'guard_name' => 'sanctum'],
                ['name' => 'deliverynotes_create', 'guard_name' => 'sanctum'],
                ['name' => 'deliverynotes_show', 'guard_name' => 'sanctum'],
                ['name' => 'deliverynotes_edit', 'guard_name' => 'sanctum'],
                ['name' => 'deliverynotes_delete', 'guard_name' => 'sanctum'],
            ],
            'Settings' => [
                ['name' => 'settings_access', 'guard_name' => 'sanctum'],
                ['name' => 'settings_create', 'guard_name' => 'sanctum'],
                ['name' => 'settings_show', 'guard_name' => 'sanctum'],
                ['name' => 'settings_edit', 'guard_name' => 'sanctum'],
                ['name' => 'settings_delete', 'guard_name' => 'sanctum']
            ],

        ];

        foreach ($modules as $key => $permissions) {
            $module = Module::create(['name' => $key]);
            foreach ($permissions as $permission)
                Permission::create(['name' => $permission['name'], 'guard_name' => $permission['guard_name'], 'module_id' => $module->id]);
        }

        $permissions = [];

        Role::insert($roles);
        // Permission::insert($permissions);
    }
}
