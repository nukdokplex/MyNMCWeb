<?php
namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([UserPermissionsSeeder::class]);
        $this->call([GroupsSeeder::class]);
        $this->call([AuditoriesSeeder::class]);
        $this->call([SpecializationsSeeder::class]);
        $this->call([UsersTableSeeder::class]);
    }
}
