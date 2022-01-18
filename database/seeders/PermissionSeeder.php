<?php

namespace Database\Seeders;

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
        $roles = ['Super Admin','Admin','Employee'];

        $permissions = ['create','show','update','delete'];

        foreach ($roles as $role) {
            $role = Role::create(['name' => $role]);
        }
        $permissionId = [];
        foreach ($permissions as $permission) {
            $permissionId[] = Permission::create(['name'=>$permission]);
        }


    }
}
