<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $hotel = \App\Models\Hotel::create(['name' => 'Harbor House', 'slug' => 'harbor-house', 'description' => 'A relaxed boutique stay near the waterfront.', 'city' => 'Portland', 'address' => '18 Wharf Street']);
        $hotel->rooms()->create(['name' => 'Garden King', 'description' => 'A quiet king room with a garden view.', 'capacity' => 2, 'nightly_rate_minor' => 14500, 'facilities' => ['Wi-Fi', 'Breakfast'], 'inventory' => 3]);
        $hotel->rooms()->create(['name' => 'Family Suite', 'description' => 'A spacious suite for small families.', 'capacity' => 4, 'nightly_rate_minor' => 22000, 'facilities' => ['Wi-Fi', 'Kitchenette'], 'inventory' => 2]);

        \App\Models\User::factory()->create(['name' => 'Demo Guest', 'email' => 'guest@example.test']);


    }
}
