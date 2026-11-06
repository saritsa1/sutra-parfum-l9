<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Katalog produk parfum eksklusif Sutra Parfum
     */
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'name' => "Rose d'Amour",
                'slug' => 'rose-damour',
                'description' => 'Mawar yang lembut dan romantis, hangat di kulit. Cocok untuk acara santai maupun formal.',
                'category_id' => 1, // Floral
                'price' => 485000,
                'img' => 'images/rose-damour.webp',
                'stock' => 12,
                'notes' => 'Mawar, peony, musk',
                'color' => '#2f5a47',
                'rating' => 4.6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Osmanthus Crystal',
                'slug' => 'osmanthus-crystal',
                'description' => 'Bunga osmanthus yang cerah dengan sentuhan buah. Ringan, manis, dan mudah disukai.',
                'category_id' => 1, // Floral
                'price' => 530000,
                'img' => 'images/osmanthus-crystal.webp',
                'stock' => 10,
                'notes' => 'Osmanthus, persik, musk bening',
                'color' => '#e07b5a',
                'rating' => 4.5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hinoki Sublime',
                'slug' => 'hinoki-sublime',
                'description' => 'Kayu hinoki yang tenang dan segar seperti hutan setelah hujan. Tahan hingga sore.',
                'category_id' => 2, // Woody
                'price' => 560000,
                'img' => 'images/hinoki-sublime.webp',
                'stock' => 8,
                'notes' => 'Hinoki, cedar, lumut',
                'color' => '#7a8a3a',
                'rating' => 4.4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Alexandra',
                'slug' => 'alexandra',
                'description' => 'Aroma bertekstur halus dengan dasar kayu yang hangat. Elegan untuk malam hari.',
                'category_id' => 2, // Woody
                'price' => 610000,
                'img' => 'images/alexandra.webp',
                'stock' => 6,
                'notes' => 'Iris, cendana, amber lembut',
                'color' => '#7f98b8',
                'rating' => 4.8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chronic Tonic',
                'slug' => 'chronic-tonic',
                'description' => 'Segar dan bersemangat, seperti tonik herbal dingin. Pilihan untuk dipakai sehari-hari.',
                'category_id' => 3, // Fresh
                'price' => 420000,
                'img' => 'images/chronic-tonic.webp',
                'stock' => 15,
                'notes' => 'Jeruk, jahe, herbal',
                'color' => '#1f8a85',
                'rating' => 4.2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'High Tide Low Tide',
                'slug' => 'high-tide-low-tide',
                'description' => 'Aroma pantai saat air pasang surut, dengan akhir kayu apung yang bersih.',
                'category_id' => 3, // Fresh
                'price' => 450000,
                'img' => 'images/high-tide-low-tide.webp',
                'stock' => 9,
                'notes' => 'Garam laut, driftwood, musk',
                'color' => '#6b4a36',
                'rating' => 4.3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Velvet Oud',
                'slug' => 'velvet-oud',
                'description' => 'Kehangatan kayu gaharu berpadu dengan mawar damaskus dan amber keemasan yang memikat.',
                'category_id' => 4, // Oriental
                'price' => 650000,
                'img' => 'images/alexandra.webp',
                'stock' => 7,
                'notes' => 'Oud, mawar damaskus, amber',
                'color' => '#4a1525',
                'rating' => 4.9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Vanilla Bourbon',
                'slug' => 'vanilla-bourbon',
                'description' => 'Sentuhan vanila Madagaskar yang kaya dengan nuansa karamel hangat dan tonka bean manis.',
                'category_id' => 5, // Gourmand
                'price' => 580000,
                'img' => 'images/osmanthus-crystal.webp',
                'stock' => 11,
                'notes' => 'Vanilla bourbon, tonka bean, brown sugar',
                'color' => '#c47d38',
                'rating' => 4.7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
