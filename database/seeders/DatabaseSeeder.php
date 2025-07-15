<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // generate 10 categories
        $categories = Category::factory()->count(10)->create();


        // generate 5 users
        User::factory()
            ->count(5)
            ->has(
                Product::factory(10)->state(function () use ($categories) {
                    return ['category_id' => $categories->random()->id];
                })
            )
            ->create();

                // --- Adicionar Clientes ---
        // Gerar 20 clientes
        Client::factory()->count(20)->create(); // Gera 20 clientes com dados fictícios

    }
}
