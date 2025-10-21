<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PublisherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('publisher')->insert([
            ['name' => 'Lorem House', 'created_at' => now() ],
            ['name' => 'JoshCollins', 'created_at' => now()],
            ['name' => 'Amazon', 'created_at' => now()],
            ['name' => 'Europa editors', 'created_at' => now()],
        ]);
    }
}