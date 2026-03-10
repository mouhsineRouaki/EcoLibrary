<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookView;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $books = Book::all();

        if ($books->isEmpty()) {
            return response()->json([
                'message' => 'Failed: no books found'
            ], 404);
        }

        return response()->json([
            'books' => $books
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $book = Book::create([
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'description' => $request->input('description'),
            'author' => $request->input('author'),
            'total_quantity' => $request->input('total_quantity'),
            'available_quantity' => $request->input('available_quantity'),
            'is_active' => $request->input('is_active'),
            'category_id' => $request->input('category_id'),
        ]);

        if (! $book) {
            return response()->json([
                'message' => 'Failed: book creation failed'
            ], 400);
        }

        return response()->json([
            'message' => 'Book created successfully',
            'book' => $book
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $book = Book::with('Category')->where('id', $id)->first();

        if (! $book) {
            return response()->json([
                'message' => 'Failed: book not found'
            ], 404);
        }

        BookView::create([
            'book_id' => $book->id,
            'user_id' => $request->user()?->id,
            'viewed_at' => now(),
        ]);

        return response()->json([
            'book' => $book
        ]); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $book = Book::where('id', $id)->first();

        if (! $book) {
            return response()->json([
                'message' => 'Failed: book not found'
            ], 404);
        }

        $updated = $book->update([
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'description' => $request->input('description'),
            'author' => $request->input('author'),
            'total_quantity' => $request->input('total_quantity') ,
            'available_quantity' => $request->input('available_quantity'),
            'is_active' => $request->input('is_active'),
            'category_id' => $request->input('category_id', $book->category_id),
        ]);

        if (! $updated) {
            return response()->json([
                'message' => 'Failed: book update failed'
            ], 400);
        }

        return response()->json([
            'message' => 'Book updated successfully',
            'book' => $book
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = Book::where('id', $id)->first();

        if (! $book) {
            return response()->json([
                'message' => 'Failed: book not found'
            ], 404);
        }

        if (! $book->delete()) {
            return response()->json([
                'message' => 'Failed: book deletion failed'
            ], 400);
        }

        return response()->json([
            'message' => 'Book deleted successfully'
        ]);
    }
    public function byCategory(Request $request, string $id){
        $books = Book::where('category_id', $id)
        ->where('is_active', true)
        ->where('available_quantity' , '>' , 0)
        ->get();

        if ($books->isEmpty()) {
            return response()->json([
                'message' => 'Failed: no books found for this category'
            ], 404);
        }

        return response()->json([
            'books' => $books
        ]);
    }
    public function search(Request $request){
        $q = trim($request->input('title' ,''));
        $category = trim($request->input('category' , ''));
        $books = Book::query()
            ->with('Category')
            ->where('is_active' , true)
            ->where('available_quantity' , '>' , 0)
            ->when($q !== '' , function($query) use ($q){
                $query->where('title' , 'like' , '%' . $q . '%');
            })
            ->when($category !== '' , function($query) use ($category){
                $query->whereHas('Category' , function($query2) use ($category){
                    $query2->where('name' , 'like' , '%' . $category . '%');
                    $query2->orWhere('id' , $category);
                });
            })
            ->get();

        if ($books->isEmpty()) {
            return response()->json([
                'message' => 'Failed: no matching books found'
            ], 404);
        }

        return response()->json([
            'books' => $books
        ]);
    }
    public function popularByCategory(string $id){
        $books = Book::where('category_id', $id)
            ->where('is_active', true)
            ->where('available_quantity' , '>' , 0)
            ->with('Category')
            ->withCount('Views as populaire')
            ->orderByDesc('populaire')
            ->limit(10)
            ->get();
         if ($books->isEmpty()) {
            return response()->json(['message' => 'Failed: no popular books found'], 404);
        }

        return response()->json(['books' => $books]);

    }
    public function newByCategory(string $id){
        $books = Book::where('category_id', $id)
            ->where('is_active', true)
            ->where('available_quantity' , '>' , 0)
            ->with('Category')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
        if($books->isEmpty()) {
            return response()->json(['message' => 'Failed: no new books found'], 404);
        }

        return response()->json(['books' => $books]);
        
    }
    public function collectionStats()
    {
        $topBooks = Book::with('Category')
            ->withCount('Views as views_count')
            ->orderByDesc('views_count')
            ->limit(10)
            ->get();

        $totals = Book::selectRaw('
                COUNT(*) as total_books,
                COALESCE(SUM(total_quantity), 0) as total_quantity,
                COALESCE(SUM(available_quantity), 0) as total_available,
                COALESCE(SUM(total_quantity - available_quantity), 0) as total_degraded,
                SUM(CASE WHEN is_active = true THEN 1 ELSE 0 END) as active_books,
                SUM(CASE WHEN is_active = false THEN 1 ELSE 0 END) as inactive_books
            ')
            ->first();

        return response()->json([
            'collection' => [
                'total_books' => (int) $totals->total_books,
                'total_quantity' => (int) $totals->total_quantity,
                'total_available' => (int) $totals->total_available,
                'total_degraded' => (int) $totals->total_degraded,
                'active_books' => (int) $totals->active_books,
                'inactive_books' => (int) $totals->inactive_books,
            ],
            'top_viewed_books' => $topBooks,
        ]);
    }

    public function degradedBooksStats()
    {
        $books = Book::with('Category')
            ->select('*')
            ->selectRaw('(total_quantity - available_quantity) as degraded_quantity')
            ->whereRaw('(total_quantity - available_quantity) > 0')
            ->orderByDesc('degraded_quantity')
            ->get();

        if ($books->isEmpty()) {
            return response()->json([
                'message' => 'Failed: no degraded books found'
            ], 404);
        }

        return response()->json([
            'books' => $books,
            'total_degraded_quantity' =>$books->sum('degraded_quantity'),
        ]);
    }

}
