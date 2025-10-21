<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;
use App\Models\Books;
use App\Models\customer_reviews;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\MiscellaneousController;
use PhpParser\Node\Expr\Cast\Object_;


class BooksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $AuthController = new AuthController();
        // ID: 1 = Create a new book
        $permission = $AuthController->get_user_permissions( 1 );

        // Get 5 books ramdolly from the database with left join to publishers
        $books = Books::leftJoin('publisher', 'books.publisher_id', '=', 'publisher.id')
        ->leftJoin('author', 'books.author_id', '=', 'author.id')
        ->leftJoin('category', 'books.category_id', '=', 'category.id')
        ->leftJoin('users', 'books.user_id', '=', 'users.id')
        ->select('books.*', 
            'publisher.name as publisher_name',
            'author.name as author_name',
            'category.name as category_name',
            'users.name as user_name',
            DB::raw('DATEDIFF(books.borrow_due_date, NOW()) as due_days'),
            DB::raw('(SELECT AVG(rating_stars) FROM customer_reviews WHERE customer_reviews.book_id = books.id) as average_rating'),
        )->inRandomOrder()->limit(5)->get();

        // Return the books to the view
        return inertia('books', [ 'books' => $books, 'create_book_permission' => $permission ]);
    }

    // search for books
    public function search( Request $request )
    {
        $search = $request->input('name');
        $books = Books::where('title', 'LIKE', "%$search%")->get();

        return response()->json(
            [
                'result' => $books,
            ]
        );
    }

    /**
     * Create the book view
     */
    public function create()
    {
        $AuthController = new AuthController();
        // ID: 1 = Create a new book
        $permission = $AuthController->get_user_permissions( 1 );

        // Fetch data from MiscellaneousController
        $miscellaneousController = new MiscellaneousController();
        $miscellaneous = $miscellaneousController->getMiscellaneousData();

        // Pass the data to the Inertia view
        return inertia('books/create', [
            'miscellaneous' => $miscellaneous,
            'permission' => $permission,
        ]);
    }

    /**
     * Create a new book by using a request.
     */
    public function create_request( Request $request )
    {

        $AuthController = new AuthController();
        // ID: 1 = Create a new book
        $permission = $AuthController->get_user_permissions( 1 );

        if ( ! $permission ) {
            return response()->json(
                [
                    'error' => 'You do not have permission to create a new book',
                ]
            );
        }
        
        // Validate the request
        $validator = $request->validate([
            'author' => 'required',
            'category' => 'required|integer',
            'description' => 'required',
            'isbn' => 'required|integer',
            'page_count' => 'required|integer',
            'publication_date' => 'required|date',
            'publisher' => 'required|integer',
            'title' => 'required',
            'cover_image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
        ]);

        // check if the validator is valid
        if ( $validator ) {
            $path = null;

            // Storing the image
            if ( $request->hasFile('cover_image') ) {
                $path = $request->file('cover_image')->storeAs(
                    'cover_images',
                    time() . '_' . $request->file('cover_image')->getClientOriginalName(),
                    'public'
                );
            }

            // Check if the book already exists
            $id = $request->input('book_id');

            if ( $id ) {
                // If the book exists, update it
                $book = Books::findOrFail($id);
                $book->update([
                    'author_id' => htmlspecialchars( $request->input('author') ),
                    'category_id' => htmlspecialchars( $request->input('category') ),
                    'description' => htmlspecialchars( $request->input('description') ),
                    'isbn' => htmlspecialchars( $request->input('isbn') ),
                    'page_count' => htmlspecialchars( $request->input('page_count') ),
                    'publication_date' => htmlspecialchars( $request->input('publication_date') ),
                    'publisher_id' => htmlspecialchars( $request->input('publisher') ),
                    'title' => htmlspecialchars( $request->input('title') ),
                    'user_id' => Auth::user()->id,
                    'cover_image' => $path ?? null,
                ]);

                // If the book is created, return a success message
                if ( $book ) {
                    return response()->json(
                        [
                            'success' => 'Book edited successfully',
                        ]
                    );
                }
            } else {
                // Create the book
                $book = Books::create([
                    'author_id' => htmlspecialchars( $request->input('author') ),
                    'category_id' => htmlspecialchars( $request->input('category') ),
                    'description' => htmlspecialchars( $request->input('description') ),
                    'isbn' => htmlspecialchars( $request->input('isbn') ),
                    'page_count' => htmlspecialchars( $request->input('page_count') ),
                    'publication_date' => htmlspecialchars( $request->input('publication_date') ),
                    'publisher_id' => htmlspecialchars( $request->input('publisher') ),
                    'title' => htmlspecialchars( $request->input('title') ),
                    'user_id' => Auth::user()->id,
                    'cover_image' => $path ?? null,
                ]);

                // If the book is created, return a success message
                if ( $book ) {
                    return response()->json(
                        [
                            'success' => 'Book created successfully',
                        ]
                    );
                }
            }
        } else {
            // If the book is not created, return an error message
            return response()->json(
                $validator
            );
        }
    }

    /**
     * Edit a specified book
     */
    public function edit( $id )
    {
        $AuthController = new AuthController();
        // ID: 1 = Create a new book
        $permission = $AuthController->get_user_permissions( 1 );

        // Fetch data from MiscellaneousController
        $miscellaneousController = new MiscellaneousController();
        $miscellaneous = $miscellaneousController->getMiscellaneousData();

        // Get the book by ID
        $book = Books::findOrFail($id);

        // Pass the data to the Inertia view
        return inertia('books/edit', [
            'book' => $book,
            'miscellaneous' => $miscellaneous,
            'permission' => $permission,
        ]);
    }

    public function show( $id )
    {
        // Get the book by ID
        $book = Books::findOrFail($id );

        // Get the customer reviews
        $customer_reviews = customer_reviews::leftJoin('users', 'users.id', '=', 'customer_reviews.user_id')
        ->select('customer_reviews.*', 'users.name as user_name' )->where('book_id', '=', $id)->get();
        
        // Pass the data to the Inertia view
        return inertia('books/show', [
            'book' => $book,
            'customer_reviews' => $customer_reviews,
        ]);
    }

    /**
     * Mark a book as borrowed by the user
     */
    public function mark_checkout( Request $request ) {
        // Validate the request
        $validator = $request->validate([
            'book_id' => 'required|integer',
        ]);

        // check if the validator is valid
        if ( $validator ) {
            // Get the book by ID
            $book = Books::findOrFail($request->input('book_id') );

            // Update the book
            $query = $book->newQuery()->where('id', $book->id);
            $query->update([
                'status' => 'borrowed',
                'user_id' => Auth::user()->id,
                'borrow_start_date' => now(),
                'borrow_due_date' => now()->addDays(5),
            ]);

            // If the book is updated, return a success message
            if ( $book ) {
                return response()->json(
                    [
                        'success' => 'Book marked successfully',
                    ]
                );
            }
        } else {
            // If the book is not updated, return an error message
            return response()->json(
                $validator
            );
        }
    }

    /**
     * Checkout book
     */
    public function mark_as_returned( Request $request ) {
        // Validate the request
        $validator = $request->validate([
            'book_id' => 'required|integer',
        ]);

        // check if the validator is valid
        if ( $validator ) {
            // Get the book by ID
            $book = Books::findOrFail($request->input('book_id') );

            // Update the book
            $query = $book->newQuery()->where('id', $book->id);
            $query->update([
                'status' => 'available',
                'user_id' => 0,
                'borrow_start_date' => null,
                'borrow_due_date' => null,
            ]);

            // If the book is updated, return a success message
            if ( $book ) {
                return response()->json(
                    [
                        'success' => 'Book marked as returned successfully',
                    ]
                );
            }
        } else {
            // If the book is not updated, return an error message
            return response()->json(
                $validator
            );
        }
    }

    /**
     * Remove the book
     */
    public function destroy( Request $request ) {
        // Get the book by ID
        $book = Books::findOrFail($request->input('book_id'));

        // Delete the book
        $book->delete();

        // If the book is deleted, return a success message
        if ( $book ) {
            return response()->json(
                [
                    'success' => 'Book deleted successfully',
                ]
            );
        }
    }
}
