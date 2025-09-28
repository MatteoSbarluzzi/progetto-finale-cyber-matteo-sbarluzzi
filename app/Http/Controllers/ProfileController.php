<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{

    // Policy: l'utente può modificare solo il proprio profilo
    public function edit(Request $request)
    {
        $user = $request->user();

        // Autorizzazione via Policy
        $this->authorize('updateProfile', $user);

        return view('profile.edit', ['user' => $user]);
    }

    // Update volutamente vulnerabile per la Challenge 6 (mass assignment)
    public function updateVulnerable(Request $request)
    {
        $user = $request->user();

        // Autorizzazione via Policy
        $this->authorize('updateProfile', $user);

        // Mass assignment non filtrato — lasciato apposta per la challenge
        $user->update($request->all());

        return back()->with('status', 'Profilo aggiornato (VULN)');
    }
}
