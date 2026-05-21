<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole =  Role::firstOrCreate(['name' => 'admin']);
        $writerRole = Role::firstOrCreate(['name' => 'writer']);
        $readerRole = Role::create(['name' => 'reader']);

        $admin = User::firstOrCreate([
            'name' => 'Admin',
            'email' => 'hello@gmail.com',
            'password' => ('12345678')
        ]);
        $admin->assignRole($adminRole);

        $writer = User::firstOrCreate([
            'name' => 'Writer',
            'email' => 'welcome@gmail.com',
            'password' => ('87654321')
        ]);
        $writer->assignRole($writerRole);

        $reader = User::firstOrCreate([
            'name' => 'Reader',
            'email' => 'bayee@gmail.com',
            'password' => ('12341234')
        ]);
        $reader->assignRole($readerRole);
    }
}
