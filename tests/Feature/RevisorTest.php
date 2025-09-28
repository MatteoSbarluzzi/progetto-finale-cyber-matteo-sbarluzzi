<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class RevisorTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        // Categoria necessaria per l'articolo
        $this->category = Category::create(['name' => 'news']);
    }

    protected function makeDraftArticle(User $author): Article
    {
        return Article::create([
            'title'       => 'Bozza',
            'subtitle'    => 'Sub',
            'body'        => 'Testo',
            'image'       => 'public/images/test.jpg',
            'category_id' => $this->category->id,
            'user_id'     => $author->id,
            'slug'        => Str::slug('Bozza-'.uniqid()),
            'is_accepted' => null,
        ]);
    }

    public function test_revisor_can_accept_article(): void
    {
        $author  = User::factory()->create(['is_writer' => true]);
        $revisor = User::factory()->create(['is_revisor' => true]);

        $article = $this->makeDraftArticle($author);

        $res = $this->actingAs($revisor)->post(route('revisor.acceptArticle', $article));
        $res->assertRedirect(route('revisor.dashboard'));

        $this->assertDatabaseHas('articles', [
            'id'          => $article->id,
            'is_accepted' => true,
        ]);
    }

    public function test_non_revisor_cannot_accept_article(): void
    {
        $author = User::factory()->create(['is_writer' => true]);
        $user   = User::factory()->create(['is_revisor' => false]);

        $article = $this->makeDraftArticle($author);

        $res = $this->actingAs($user)->post(route('revisor.acceptArticle', $article));

        $res->assertStatus(403); 
    }
}
