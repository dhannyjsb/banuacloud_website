<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_USER_EMAIL', 'admin@banuacloud.com');

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_USER_NAME', 'Banua Admin'),
                'password' => env('ADMIN_USER_PASSWORD', 'Admin12345!'),
                'role' => 'admin',
                'email_verified_at' => Carbon::now(),
            ],
        );
    }
}
