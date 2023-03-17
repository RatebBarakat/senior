<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Admin;
use App\Models\BloodType;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Dotenv\Util\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Gate;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $allPermission = [
            'admins' => 'm,c,r,u,d',
            'roles' => 'm,c,r,u,d',
            'centers' => 'm,c,r,u,d',
            'appointments' => 'm,r,u',
            'employees' => 'm,c,d'
        ];
        $rolesArray = [
            'super admin' => [
                'admins' => 'm,c,r,u,d',
                'roles' => 'm,c,r,u,d',
                'centers' => 'm,c,r,u,d',
            ],
            'center admin' => [
                'employees' => 'm,c,d'
            ],
            'center employee' => [
                'appointments' => 'm,r,u',
            ]
        ];

        $permissionsMap = [
            'm' => 'manage',
            'c' => 'create',
            'r' => 'read',
            'u' => 'update',
            'd' => 'delete'
        ];

        foreach ($allPermission as $type => $content){//create permissions based on all perimission array
            foreach (explode(",", $content) as $permission) {
                $name =  $permissionsMap[$permission] . "-" .$type;
                \App\Models\Permission::create([
                    'name' => $name,
                    'slug' => \Illuminate\Support\Str::slug($name)
                ]);
            }
        }

        foreach ($rolesArray as $roleName => $rolePermissions) {//attach permission to actor based on their role
            $role = Role::create(['name' => \Illuminate\Support\Str::slug($roleName)]);
            foreach ($rolePermissions as $type => $content){
                foreach (explode(",", $content) as $permission) {
                    $name =  $permissionsMap[$permission]. "-$type";
                    $perm = \App\Models\Permission::where(['name' => $name])->first();
                    $role->permissions()->attach($perm);
                }
            }
        }

        $superAdmin = Admin::create([
            'name' => 'super',
            'email' => 'super@gmail.com',
            'password' => '$2y$10$SU7fXZaVS6ArumU9zCiu0OExbt9dJ.3OqEwGIBsPU2GbZL87yFuMy'
        ]);
        $superAdmin->attachRole('super-admin');

        $blood_types = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        foreach ($blood_types as $blood_type){
            BloodType::create(['blood_type' => $blood_type]);
        }

        $locations = [
            'tripoli' => ['tripoli' => ['latitude' => '1.1', 'longitude' => '2.2']],
            'beirut' => ['beirut' => ['latitude' => '1.145', 'longitude' => '3.2']],
            'saida' => ['saida' => ['latitude' => '12.145', 'longitude' => '33.2']],
        ];

        foreach ($locations as $city => $location) {
            foreach ($location as $name => $coords) {
                \App\Models\Location::create([
                    'name' => $name,
                    'city' => $city,
                    'latitude' => $coords['latitude'],
                    'longitude' => $coords['longitude'],
                ]);
            }
        }
    }
}
