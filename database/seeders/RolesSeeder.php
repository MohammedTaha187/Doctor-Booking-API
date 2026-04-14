<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guards = ['api', 'web'];

        foreach ($guards as $guard) {
            Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
            Role::firstOrCreate(['name' => 'doctor', 'guard_name' => $guard]);
            Role::firstOrCreate(['name' => 'patient', 'guard_name' => $guard]);
        }
    }
}
