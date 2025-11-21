<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profile_data = [
            'language' => 'en',
            'image' => 'uploads/profile-images/default.png',
            'fcm_token' => '',
            'timezone' => 'Europe/London',
            'email_notify' => '1',
            'sms_notify' => '1',

            'addresses'=> [],
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
        ];

        $root = User::create([
            'name' => [ 'full_name' => 'Root Developer' ],
            'username' => 'root',
            'email' => 'root@spotprint.co.uk',
            'phone' => '+1234567891',
            'password' => '12345678',
            'status' => 'Active',
            'profile' => $profile_data,

            "email_verified_at" => date('Y-m-d H:i:s'),
            "phone_verified_at" => date('Y-m-d H:i:s'),
        ]);

        $admin = User::create([
            'name' => [ 'full_name' => 'Admin User' ],
            'username' => 'admin',
            'email' => 'admin@spotprint.co.uk',
            'phone' => '+1234567891',
            'password' => '12345678',
            'status' => 'Active',
            'profile' => $profile_data,

            "email_verified_at" => date('Y-m-d H:i:s'),
            "phone_verified_at" => date('Y-m-d H:i:s'),
        ]);

        $user = User::create([
            'name' => [ 'full_name' => 'Dev User' ],
            'username' => 'user',
            'email' => 'user@spotprint.co.uk',
            'phone' => '+1234567891',
            'password' => '12345678',
            'status' => 'Active',
            'profile' => $profile_data,

            "email_verified_at" => date('Y-m-d H:i:s'),
            "phone_verified_at" => date('Y-m-d H:i:s'),
        ]);


        $permissions = [
            'all.manage'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $user_permissions = [];


        $root_role = Role::create(['name' => 'Root']);
        $admin_role = Role::create(['name' => 'Admin']);
        $user_role = Role::create(['name' => 'User']);


        $permissions_all = Permission::pluck('id', 'id')->all();
        $root_role->syncPermissions($permissions_all);
        $admin_role->syncPermissions($permissions_all);
        $user_role->syncPermissions($user_permissions);


        $root->assignRole([$root_role->id]);
        $admin->assignRole([$admin_role->id]);
        $user->assignRole([$user_role->id]);
    }
}
