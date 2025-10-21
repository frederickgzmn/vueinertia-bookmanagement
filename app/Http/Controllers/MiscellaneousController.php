<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Author;
use App\Models\Publisher;

class MiscellaneousController extends Controller
{
    /**
     * Display a listing of the resource: Categories, Authors and publishers.
     */
    public function getMiscellaneousData()
    {
        $category = Category::all();
        $author = Author::all();
        $publisher = Publisher::all();

        return [
            'category' => $category,
            'author' => $author,
            'publisher' => $publisher,
        ];
    }
}
