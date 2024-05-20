<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();


        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'panel_user']);

        $user = \App\Models\User::factory()->create([
            'nip' => '1',
            'username' => 'admin',
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('jika12345'),
        ]);
        $user->assignRole('super_admin');

        $user2 = \App\Models\User::factory()->create([
            'nip' => '1',
            'username' => 'User',
            'name' => 'User',
            'email' => 'user@gmail.com',
            'password' => bcrypt('jika12345'),
        ]);
        $user2->assignRole('panel_user');
    }
}
