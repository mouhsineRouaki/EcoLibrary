<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookView;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $users = User::factory(20)->create();
        $categories = Category::factory(8)->create();

        $categories->each(function (Category $category) use ($users): void {
            $books = Book::factory(rand(8, 16))->create([
                'category_id' => $category->id,
            ]);

            $books->each(function (Book $book) use ($users): void {
                BookView::factory(rand(5, 45))->create([
                    'book_id' => $book->id,
                    'user_id' => $users->random()->id,
                ]);

                BookView::factory(rand(0, 12))->create([
                    'book_id' => $book->id,
                    'user_id' => null,
                ]);
            });
        });
    }
}
