<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->firstOrNew(['email' => 'admin@gmail.com']);
        $admin->name = 'Admin';

        if (! $admin->exists) {
            $admin->password = 'admin123';
        }

        $admin->forceFill(['role' => 'admin'])->save();
    }
}
