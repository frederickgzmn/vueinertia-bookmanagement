<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('category')->insert([
            ['name' => 'Dark', 'created_at' => now() ],
            ['name' => 'Terror', 'created_at' => now()],
            ['name' => 'Sci-fi', 'created_at' => now()],
            ['name' => 'Aventure', 'created_at' => now()],
            ['name' => 'Fantasy', 'created_at' => now()],
            ['name' => 'Documental', 'created_at' => now()]
        ]);
    }
}
