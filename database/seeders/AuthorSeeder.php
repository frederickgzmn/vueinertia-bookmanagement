<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('author')->insert([
            ['name' => 'Jose Rowling', 'created_at' => now() ],
            ['name' => 'George R.R. Martin', 'created_at' => now()],
            ['name' => 'J.R.R. Tolkien', 'created_at' => now()],
            ['name' => 'Agatha Christie', 'created_at' => now()],
            ['name' => 'Stephen King', 'created_at' => now()],
            ['name' => 'Mark Twain', 'created_at' => now()]
        ]);
    }
}
