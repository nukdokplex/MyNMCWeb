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

        //Permission::create(['name' => ""]);

        //Users CRUD
        Permission::create(['name' => "create users"]);
        Permission::create(['name' => "read users"]);
        Permission::create(['name' => "update users"]);
        Permission::create(['name' => "update self"]);
        Permission::create(['name' => "delete users"]);
        Permission::create(['name' => "delete self"]);

        //Users non-CRUD
        Permission::create(['name' => "update self email"]);
        Permission::create(['name' => "update self name"]);
        Permission::create(['name' => "update self password"]);

        //Posts CRUD
        Permission::create(['name' => "create posts"]);
        Permission::create(['name' => "read posts"]);
        Permission::create(['name' => "update posts"]);
        Permission::create(['name' => "update self posts"]);
        Permission::create(['name' => "delete posts"]);
        Permission::create(['name' => "delete self posts"]);

        //Comments CRUD
        Permission::create(['name' => "create comments"]);
        Permission::create(['name' => "read comments"]);
        Permission::create(['name' => "update comments"]);
        Permission::create(['name' => "update self comments"]);
        Permission::create(['name' => "delete comments"]);
        Permission::create(['name' => "delete self comments"]);

        //Schedule CRUD
        Permission::create(['name' => "create schedule"]);
        Permission::create(['name' => "read schedule"]);
        Permission::create(['name' => "update schedule"]);
        Permission::create(['name' => "delete schedule"]);

        //Group CRUD
        Permission::create(['name' => "create groups"]);
        Permission::create(['name' => "read groups"]);
        Permission::create(['name' => "read self group"]);
        Permission::create(['name' => "update groups"]);
        Permission::create(['name' => "delete groups"]);

        //Group non-CRUD
        Permission::create(['name' => "add group member"]);
        Permission::create(['name' => "remove group member"]);
        Permission::create(['name' => "add self group member"]);
        Permission::create(['name' => "remove self group member"]);

        //Permissions CRUD
        Permission::create(['name' => "create permission"]);
        Permission::create(['name' => "read permission"]);
        Permission::create(['name' => "update permission"]);
        Permission::create(['name' => "delete permission"]);

        //Permissions non-CRUD
        Permission::create(['name' => "give permissions to users"]);
        Permission::create(['name' => "remove permissions from users"]);
        Permission::create(['name' => "give permissions to roles"]);
        Permission::create(['name' => "remove permissions from roles"]);

        //Roles CRUD
        Permission::create(['name' => "create roles"]);
        Permission::create(['name' => "read roles"]);
        Permission::create(['name' => "update roles"]);
        Permission::create(['name' => "delete roles"]);

        //Roles non-CRUD
        Permission::create(['name' => "give roles"]);
        Permission::create(['name' => "remove roles"]);

        //Roles

        //Guest
        $guest = Role::create(['name' => "guest"]);
        $guest->givePermissionTo([
            //Users
            //none

            //Posts
            "read posts",

            //Comments
            //none

            //Schedule
            "read schedule",

            //Group
            //none

            //Permissions
            //none

            //Roles
            //none
        ]);

        //Student
        $student = Role::create(['name' => "student"]);
        $student->givePermissionTo([
            //Users
            "update self email",
            "update self password",

            //Posts
            "create posts",
            "read posts",
            "update self posts",

            //Comments
            "create comments",
            "read comments",
            "update self comments",

            //Schedule
            "read schedule",

            //Group
            "read self group",

            //Permissions
            //none

            //Roles
            //none
        ]);

        //Teacher
        $teacher = Role::create(['name' => "teacher"]);
        $teacher->givePermissionTo([
            //Users
            "update self email",
            "update self password",

            //Posts
            "create posts",
            "read posts",
            "update self posts",

            //Comments
            "create comments",
            "read comments",
            "update self comments",

            //Schedule
            "read schedule",

            //Group
            "read self group",

            //Permissions
            //none

            //Roles
            //none
        ]);

        //Group teacher
        $group_teacher = Role::create(['name' => "group teacher"]);
        $group_teacher->givePermissionTo([
            //Users
            "update self email",
            "update self password",

            //Posts
            "create posts",
            "read posts",
            "update self posts",

            //Comments
            "create comments",
            "read comments",
            "update self comments",

            //Schedule
            "read schedule",

            //Group
            "read self group",
            "add self group member"

            //Permissions
            //none

            //Roles
            //none
        ]);

        //Administration
        $group_teacher = Role::create(['name' => "administration"]);
        $group_teacher->givePermissionTo([
            //Users
            "update self email",
            "update self password",

            //Posts
            "create posts",
            "read posts",
            "update posts",
            "update self posts",
            "delete posts",
            "delete self posts",

            //Comments
            "create comments",
            "read comments",
            "update self comments",

            //Schedule
            "create schedule",
            "read schedule",
            "update schedule",
            "delete schedule",

            //Group
            "create groups",
            "read groups",
            "update groups",
            "delete groups",
            "add group member",
            "remove group member"

            //Permissions
            //none

            //Roles
            //none
        ]);

        //Architect
        $architect = Role::create(['name' => "architect"]);
        $architect->givePermissionTo(Permission::all());

    }
}
