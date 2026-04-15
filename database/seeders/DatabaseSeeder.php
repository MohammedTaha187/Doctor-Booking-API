<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            LocaleSeeder::class,
            SpecialtySeeder::class,
            DoctorSeeder::class,
            TimeSlotSeeder::class,
            AppointmentSeeder::class,
            PaymentSeeder::class,
            ReviewSeeder::class,
            TranslationSeeder::class,
            SettingSeeder::class,
        ]);

        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}
