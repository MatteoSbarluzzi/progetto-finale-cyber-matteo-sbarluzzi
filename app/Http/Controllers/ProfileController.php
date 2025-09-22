<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    
    public function edit(Request $request)
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function updateVulnerable(Request $request)
    {
        $user = $request->user();

        // Mass assignment non filtrato per la challenge 6
        $user->update($request->all());

        return back()->with('status', 'Profilo aggiornato (VULN)');
    }
}
