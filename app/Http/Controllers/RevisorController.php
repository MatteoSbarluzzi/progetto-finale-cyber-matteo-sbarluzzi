<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class RevisorController extends Controller
{
    /**
     * Dashboard del revisore.
     * Policy: ArticlePolicy@review (abilità senza target, si passa la classe).
     */
    public function dashboard()
    {
        $this->authorize('review', Article::class);

        $unrevisionedArticles = Article::whereNull('is_accepted')->get();
        $acceptedArticles     = Article::where('is_accepted', true)->get();
        $rejectedArticles     = Article::where('is_accepted', false)->get();

        return view('revisor.dashboard', compact('unrevisionedArticles', 'acceptedArticles', 'rejectedArticles'));
    }

    /**
     * Accetta e pubblica un articolo.
     * Policy: ArticlePolicy@publish (revisor o admin).
     */
    public function acceptArticle(Article $article)
    {
        $this->authorize('publish', $article);

        $article->is_accepted = true;
        $article->save();

        return redirect(route('revisor.dashboard'))->with('message', 'Article Published');
    }

    /**
     * Rifiuta un articolo.
     * Policy: ArticlePolicy@publish (revisor o admin).
     */
    public function rejectArticle(Article $article)
    {
        $this->authorize('publish', $article);

        $article->is_accepted = false;
        $article->save();

        return redirect(route('revisor.dashboard'))->with('message', 'Article Declined');
    }

    /**
     * Rimette un articolo in revisione.
     * Policy: ArticlePolicy@publish (revisor o admin).
     */
    public function undoArticle(Article $article)
    {
        $this->authorize('publish', $article);

        $article->is_accepted = null;
        $article->save();

        return redirect(route('revisor.dashboard'))->with('message', 'Article back to review');
    }
}
