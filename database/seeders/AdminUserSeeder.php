<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@ia-admin.com'],
            [
                'name'              => 'IA Administrator',
                'password'          => Hash::make('Admin@1234!'),
                'is_admin'          => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user seeded: admin@ia-admin.com / Admin@1234!');
    }
}
