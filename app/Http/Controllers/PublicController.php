<?php

namespace App\Http\Controllers;

use App\Mail\CareerRequestMail;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PublicController extends Controller
{
    public function __construct()
    {
        // Autenticazione su tutte le azioni tranne la homepage
        $this->middleware('auth')->except(['homepage']);
    }

    public function homepage()
    {
        $articles = Article::where('is_accepted', true)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        return view('welcome', compact('articles'));
    }

    public function careers()
    {
        return view('careers');
    }

    public function careersSubmit(Request $request)
    {
        $request->validate([
            'role'    => 'required|in:admin,revisor,writer',
            'email'   => 'required|email',
            'message' => 'required',
        ]);

        $user    = Auth::user();
        $role    = $request->role;
        $email   = $request->email;
        $message = $request->message;

        // Evita richieste duplicate tramite policies che già usi nel resto dell’app
        switch ($role) {
            case 'admin':
                if ($user->can('manageAdminArea', User::class)) {
                    return back()->with('alert', 'Hai già i permessi da amministratore.');
                }
                $user->is_admin = null;
                break;

            case 'revisor':
                if ($user->can('review', \App\Models\Article::class)) {
                    return back()->with('alert', 'Hai già i permessi da revisore.');
                }
                $user->is_revisor = null;
                break;

            case 'writer':
                if ($user->can('create', \App\Models\Article::class)) {
                    return back()->with('alert', 'Hai già i permessi da writer.');
                }
                $user->is_writer = null;
                break;
        }

        Mail::to('admin@theaulabpost.it')->send(new CareerRequestMail(compact('role','email','message')));

        $user->update();

        return redirect(route('homepage'))->with('message', 'Mail inviata con successo!');
    }
}
