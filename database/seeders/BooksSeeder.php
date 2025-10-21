<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BooksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('books')->insert([
            'title' => 'Lorem Ipsum',
            'author_id' => 1,
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'cover_image' => 'cover_images/1745801503_9781616208882.jpg',
            'isbn' => '9783161484100',
            'publisher_id' => 1,
            'category_id' => 1,
            'user_id' => 0,
            'publication_date' => now(),
            'page_count' => 300,
            'status' => 'available',
            'created_at' => now()
        ]);

        DB::table('books')->insert([
            'title' => 'Lorem 2',
            'author_id' => 2,
            'description' => 'Lorem elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'cover_image' => 'cover_images/1745801503_9781616208882.jpg',
            'isbn' => '9783161484100',
            'publisher_id' => 1,
            'category_id' => 1,
            'user_id' => 0,
            'publication_date' => now(),
            'page_count' => 300,
            'status' => 'available',
            'created_at' => now()
        ]);
    }
}
