<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\customer_reviews;

class CustomerReviewsController extends Controller
{
    public function review_create ( Request $request )
    {
        // Validate the request
        $validator = $request->validate([
            'title' => 'required|string|max:255',
            'comment' => 'required|string|max:1000',
            'rating_stars' => 'required|integer|min:1|max:5',
        ]);

        if ( $validator ) {
            customer_reviews::create([
                'title' => htmlspecialchars( $request->input('title') ),
                'comment' => htmlspecialchars( $request->input('comment') ),
                'rating_stars' => htmlspecialchars( $request->input('rating_stars') ),
                'user_id' => auth()->user()->id,
                'book_id' => htmlspecialchars( $request->input('book_id') ),
            ]);

            return response()->json([
                'message' => 'Review created successfully',
            ]);
        }
    }
}
