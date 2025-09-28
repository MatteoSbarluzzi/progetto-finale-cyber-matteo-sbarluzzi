<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    // Tutti possono vedere la lista e i singoli articoli pubblici
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Article $article): bool
    {
        return true;
    }

    // Scrivere articoli: solo chi ha ruolo writer
    public function create(User $user): bool
    {
        return (bool) $user->is_writer;
    }

    // Modificare: autore dell'articolo oppure admin
    public function update(User $user, Article $article): bool
    {
        return $user->id === $article->user_id || (bool) $user->is_admin;
    }

    // Eliminare: di default solo admin
    public function delete(User $user, Article $article): bool
    {
        return (bool) $user->is_admin;
    }

    // Invio in revisione: solo autore (writer)
    public function submitForReview(User $user, Article $article): bool
    {
        return $user->id === $article->user_id && (bool) $user->is_writer;
    }

    // Azioni da revisor: vedere la coda, marcare approvato/rifiutato, pubblicare
    public function review(User $user): bool
    {
        return (bool) $user->is_revisor;
    }

    public function publish(User $user, Article $article): bool
    {
        return (bool) $user->is_revisor || (bool) $user->is_admin;
    }

    public function restore(User $user, Article $article): bool
    {
        return (bool) $user->is_admin;
    }

    public function forceDelete(User $user, Article $article): bool
    {
        return (bool) $user->is_admin;
    }
}
