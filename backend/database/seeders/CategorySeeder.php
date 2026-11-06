<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Kategori golongan aroma parfum untuk Sutra Parfum
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            ['category' => 'Floral', 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'Woody', 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'Fresh', 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'Oriental', 'created_at' => now(), 'updated_at' => now()],
            ['category' => 'Gourmand', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
