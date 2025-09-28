<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class WriterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Categoria minima per creare articoli
        Category::create(['name' => 'news']);
    }

    public function test_writer_can_access_writer_dashboard(): void
    {
        $writer = User::factory()->create(['is_writer' => true]);

        $res = $this->actingAs($writer)->get('/writer/dashboard');

        $res->assertStatus(200);
    }

    public function test_non_writer_cannot_access_writer_dashboard(): void
    {
        $user = User::factory()->create(['is_writer' => false]);

        $res = $this->actingAs($user)->get('/writer/dashboard');

        $res->assertStatus(403); // policy create su Article::class blocca
    }

    public function test_writer_can_open_create_article_page(): void
    {
        $writer = User::factory()->create(['is_writer' => true]);

        $res = $this->actingAs($writer)->get('/articles/create');

        $res->assertStatus(200);
    }

    public function test_non_writer_cannot_open_create_article_page(): void
    {
        $user = User::factory()->create(['is_writer' => false]);

        $res = $this->actingAs($user)->get('/articles/create');

        $res->assertStatus(403);
    }
}
