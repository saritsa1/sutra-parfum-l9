<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // User uji coba sesuai modul
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        // is_admin tidak ada di $fillable (supaya tidak bisa diisi lewat registrasi), jadi diisi paksa di sini
        $admin->forceFill(['is_admin' => true])->save();

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Panggil seeder categories dan products
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
