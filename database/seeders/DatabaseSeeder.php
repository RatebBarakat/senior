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

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $allPermission = [
            'categories' => 'c,r,u,d',
            'users' => 'c,r,u,d',
            'roles' => 'c,r,u,d'
        ];
        $rolesArray = [
            'admin' => [
                'categories' => 'c,r',
                'users' => 'c,r',
                'roles' => 'r'
            ],
            'super admin' => [
                '*' => '*'
            ],
            'user' => [
                'categories' => 'r',
                'users' => 'r',
            ],
        ];
        $permissionsMap = [
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
                if ($type == 'super-admin' || $content == '*'){
                    continue;
                }else{
                    foreach (explode(",", $content) as $permission) {
                        $name =  $permissionsMap[$permission]. "-$type";
                        $perm = \App\Models\Permission::where(['name' => $name])->first();
                        $role->permissions()->attach($perm);
                    }
                }

            }
        }

        $user = User::factory()->make([
           'name' => fake()->name(),
           'email' => fake()->email(),
           'password' => '$2y$10$SU7fXZaVS6ArumU9zCiu0OExbt9dJ.3OqEwGIBsPU2GbZL87yFuMy'
        ]);
        $user->attachRole('user');

        $admin = Admin::create([
           'name' => 'user',
           'email' => 'admin@gmail.com',
           'password' => '$2y$10$SU7fXZaVS6ArumU9zCiu0OExbt9dJ.3OqEwGIBsPU2GbZL87yFuMy'
        ]);
        $admin->attachRole('admin');

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
