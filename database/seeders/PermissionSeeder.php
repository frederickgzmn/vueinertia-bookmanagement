<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permission')->insert([
            ['name' => 'Create New Book', 'created_at' => now() ],
            ['name' => 'Edit an existing book', 'created_at' => now()],
            ['name' => 'Remove a book', 'created_at' => now()],
            ['name' => 'Mark a book as returned', 'created_at' => now()],
            ['name' => 'Submit review', 'created_at' => now()],
        ]);
    }
}
