<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role_admin = Role::create(['name' => 'admin']);
        $role_standard = Role::create(['name' => 'standard']);
        
        $permission_company = Permission::create(['name' => 'companys']);
        $permission_user = Permission::create(['name' => 'users']);
        $permission_channel = Permission::create(['name' => 'channel']);
        $permission_origem = Permission::create(['name' => 'origem']);
        $permission_funnel = Permission::create(['name' => 'funnel']);
        $permission_notification = Permission::create(['name' => 'notification']);
        $permission_state = Permission::create(['name' => 'state']);
        $permission_city = Permission::create(['name' => 'city']);
        $permission_status = Permission::create(['name' => 'status']);
        $permission_message = Permission::create(['name' => 'message']);
        
        $permission_admin = [$permission_user, 
                            $permission_company, 
                            $permission_channel,
                            $permission_origem,
                            $permission_funnel,
                            $permission_notification,
                            $permission_state,
                            $permission_city,
                            $permission_status,
                            $permission_message,
                        ];

        $role_admin->syncPermissions($permission_admin);
        $role_standard->givePermissionTo($permission_message);
    }
}
