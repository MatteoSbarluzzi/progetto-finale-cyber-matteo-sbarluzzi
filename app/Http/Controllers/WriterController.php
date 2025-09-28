<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Support\Facades\Auth;

class WriterController extends Controller
{
    // Policy: richiede che l'utente abbia il permesso di creare articoli (ArticlePolicy@create), ossia il ruolo "writer". In questo modo chi non è writer non può accedere alla dashboard.
    
    public function dashboard()
    {
        // Guard d’ingresso basata su Policy
        $this->authorize('create', Article::class);

        $articles            = Auth::user()->articles()->orderBy('created_at', 'desc')->get();
        $acceptedArticles    = $articles->where('is_accepted', true);
        $rejectedArticles    = $articles->where('is_accepted', false);
        $unrevisionedArticles = $articles->where('is_accepted', null);

        return view('writer.dashboard', compact('acceptedArticles', 'rejectedArticles', 'unrevisionedArticles'));
    }
}
