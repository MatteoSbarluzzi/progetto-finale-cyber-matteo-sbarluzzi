<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\User;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Mews\Purifier\Facades\Purifier;

class ArticleController extends Controller
{
    public function __construct()
    {
        // Le pagine pubbliche restano accessibili senza auth
        $this->middleware('auth')->except(['index', 'show', 'byCategory', 'byUser', 'articleSearch']);
    }

    // Display a listing of the resource
    public function index()
    {
        $articles = Article::where('is_accepted', true)->orderBy('created_at', 'desc')->get();
        return view('articles.index', compact('articles'));
    }

    // Show the form for creating a new resource
    public function create()
    {
        // Solo writer
        $this->authorize('create', Article::class);

        return view('articles.create');
    }

    // Store a newly created resource in storage
    public function store(Request $request)
    {
        // Solo writer
        $this->authorize('create', Article::class);

        $request->validate([
            'title' => 'required|unique:articles|min:5',
            'subtitle' => 'required|min:5',
            'body' => 'required|min:10',
            'image' => 'required|image',
            'category' => 'required',
            'tags' => 'required'
        ]);

        $article = Article::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'body' => Purifier::clean($request->body),
            'image' => $request->file('image')->store('public/images'),
            'category_id' => $request->category,
            'user_id' => Auth::id(),
            'slug' => Str::slug($request->title),
        ]);
        
        $tags = explode(',', $request->tags);
        foreach($tags as $i => $tag){ $tags[$i] = trim($tag); }

        foreach($tags as $tag){
            $newTag = Tag::updateOrCreate(['name' => strtolower($tag)]);
            $article->tags()->attach($newTag);
        }

        // Audit log: creazione articolo
        Log::channel('audit')->info('Article created', [
            'article_id' => $article->id,
            'title'      => $article->title,
            'by_user'    => Auth::id(),
            'ip'         => $request->ip(),
            'time'       => now()->toIso8601String(),
        ]);

        return redirect(route('homepage'))->with('message', 'Articolo creato con successo');
    }

    // Display the specified resource
    public function show(Article $article)
    {
        return view('articles.show', compact('article'));
    }

    // Show the form for editing the specified resource
    public function edit(Article $article)
    {
        // Autore o admin
        $this->authorize('update', $article);

        return view('articles.edit', compact('article'));
    }

    // Update the specified resource in storage.
    public function update(Request $request, Article $article)
    {
        // Autore o admin
        $this->authorize('update', $article);

        $request->validate([
            'title' => 'required|min:5|unique:articles,title,' . $article->id,
            'subtitle' => 'required|min:5',
            'body' => 'required|min:10',
            'image' => 'image',
            'category' => 'required',
            'tags' => 'required'
        ]);

        $article->update([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'body' => Purifier::clean($request->body),
            'category_id' => $request->category,
            'slug' => Str::slug($request->title),
        ]);

        if($request->image){
            Storage::delete($article->image);
            $article->update([
                'image' => $request->file('image')->store('public/images')
            ]);
        }
        
        $tags = array_map('trim', explode(',', $request->tags));

        $newTags = [];
        foreach($tags as $tag){
            $newTag = Tag::updateOrCreate(['name' => strtolower($tag)]);
            $newTags[] = $newTag->id;
        }
        $article->tags()->sync($newTags);

        // Audit log: modifica articolo
        Log::channel('audit')->info('Article updated', [
            'article_id' => $article->id,
            'title'      => $article->title,
            'by_user'    => Auth::id(),
            'ip'         => $request->ip(),
            'time'       => now()->toIso8601String(),
        ]);

        return redirect(route('writer.dashboard'))->with('message', 'Articolo modificato con successo');
    }

    // Remove the specified resource from storage
    public function destroy(Article $article)
    {
        // Solo admin (o aggiungi autore) 
        $this->authorize('delete', $article);

        $articleId = $article->id;

        foreach ($article->tags as $tag) {
            $article->tags()->detach($tag);
        }
        $article->delete();

        // Audit log: eliminazione articolo
        Log::channel('audit')->warning('Article deleted', [
            'article_id' => $articleId,
            'by_user'    => Auth::id(),
            'ip'         => request()->ip(),
            'time'       => now()->toIso8601String(),
        ]);
        
        return redirect()->back()->with('message', 'Articolo cancellato con successo');
    }

    public function byCategory(Category $category){
        $articles = $category->articles()->where('is_accepted', true)->orderBy('created_at', 'desc')->get();
        return view('articles.by-category', compact('category', 'articles'));
    }
    
    public function byUser(User $user){
        $articles = $user->articles()->where('is_accepted', true)->orderBy('created_at', 'desc')->get();
        return view('articles.by-user', compact('user', 'articles'));
    }

    public function articleSearch(Request $request){
        $query = $request->input('query');
        $articles = Article::search($query)->where('is_accepted', true)->orderBy('created_at', 'desc')->get();
        return view('articles.search-index', compact('articles', 'query'));
    }
}
