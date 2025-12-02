<?php

namespace Database\Seeders;

use App\Modules\Admin\Domain\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@neoux.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
