<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'create posts']);
        Permission::create(['name' => 'delete posts']);
        Permission::create(['name' => 'delete own posts']);
        Permission::create(['name' => 'update posts']);
        Permission::create(['name' => 'update own posts']);

        Permission::create(['name' => 'create commentaries']);
        Permission::create(['name' => 'update commentaries']);
        Permission::create(['name' => 'update own commentaries']);
        Permission::create(['name' => 'delete commentaries']);
        Permission::create(['name' => 'delete own commentaries']);

        Permission::create(['name' => 'upload files']);

        Permission::create(['name' => 'manage groups']);

        Permission::create(['name' => 'manage specializations']);

        Permission::create(['name' => 'manage subjects']);

        Permission::create(['name' => 'manage auditories']);

        Permission::create(['name' => 'manage users']);

        Permission::create(['name' => 'manage schedule']);

        Permission::create(['name' => 'manage app settings']);

        $guest = Role::create(['name' => 'guest']);
        $guest->givePermissionTo(['create commentaries']);
        $guest->save();

        $student = Role::create(['name' => 'student']);
        $student->givePermissionTo(['create posts', 'create commentaries']);
        $student->save();

        $teacher = Role::create(['name' => 'teacher']);
        $teacher->givePermissionTo([
            'create posts',
            'create commentaries',
            'upload files'
        ]);
        $teacher->save();

        $administrator = Role::create(['name' => 'administrator']);
        $administrator->givePermissionTo([
            'create posts',
            'delete posts',
            'delete own posts',
            'update posts',
            'update own posts',
            'create commentaries',
            'update commentaries',
            'update own commentaries',
            'delete commentaries',
            'delete own commentaries',
            'upload files',
            'manage groups',
            'manage specializations',
            'manage subjects',
            'manage auditories',
            'manage users',
            'manage schedule'
        ]);
        $administrator->save();

        $system_architect = Role::create(['name' => 'system architect']);
        $system_architect->givePermissionTo(Permission::all());
        $system_architect->save();


    }
}
