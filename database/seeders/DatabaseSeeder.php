<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Merk;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // User::factory(1)->create();
        // Category::factory()->create();
        // Merk::factory(1)->create();
        // Product::factory(100)->create();
        Setting::factory(1)->create();

        // $this->call(CategorySeeder::class);
    }
}