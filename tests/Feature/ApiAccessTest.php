<?php

namespace Tests\Feature\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Models\User;

class ApiAccessTest extends TestCase
{
    use RefreshDatabase;
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function test_reader_can_search_books(){
        $reader =  User::factory()->create(['role' => 'reader']);
        $category = Category::factory()->create();
        Book::factory()->create([
            'category_id' => $category->id,
            'title' => 'Laravel Clean Code',
            'available_quantity' => 3,
            'is_active' => true,
        ]);
        Sanctum::actingAs($reader);
        $response = $this->getJson('api/books');
        $response->assertStatus(200);
    }
    public function test_reader_cannot_access_admin_endpoints(){
        $reader =  User::factory()->create(['role' => 'reader']);
        Sanctum::actingAs($reader);
        $this->getJson('api/admin/stats/collection')
            ->assertStatus(403);
    }
}
