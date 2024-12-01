<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuCategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['id' => 40, 'name' => 'Starters'],
            ['id' => 42, 'name' => 'Specialities'],
            ['id' => 43, 'name' => 'Sides'],
            ['id' => 44, 'name' => 'Pasta'],
            ['id' => 45, 'name' => 'Pizza'],
            ['id' => 46, 'name' => 'Calzones'],
            ['id' => 47, 'name' => 'Salads'],
            ['id' => 48, 'name' => 'Kebabs'],
            ['id' => 49, 'name' => 'Burgers'],
            ['id' => 50, 'name' => 'Kids Menu'],
            ['id' => 59, 'name' => 'Drinks'],
            ['id' => 60, 'name' => 'Sauces'],
            ['id' => 61, 'name' => 'Desserts'],
            ['id' => 87, 'name' => 'Wraps'],
            ['id' => 88, 'name' => 'Parmesan Chicken'],
            ['id' => 89, 'name' => 'Combination Meals'],
        ];

        foreach ($categories as $category) {
            DB::table('menu_categories')->insert($category);
        }
    }
} 