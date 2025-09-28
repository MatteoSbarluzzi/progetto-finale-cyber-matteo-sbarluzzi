<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    // Aggiornare il proprio profilo (nome/email/password)
    public function updateProfile(User $user, User $target): bool
    {
        return $user->id === $target->id;
    }

    // Gestire ruoli/categorie/tags e pannello admin: equivalente del middleware UserIsAdmin
    public function manageAdminArea(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    // Assegnare o cambiare ruoli ad altri utenti
    public function assignRole(User $user, User $target): bool
    {
        if (! $user->is_admin) {
            return false;
        }

        // Non permettere di cambiare i propri ruoli
        if ($user->id === $target->id) {
            return false;
        }

        return true;
    }

    // Visualizzare dati finanziari (l’accesso di rete resta nel middleware OnlyLocalAdmin)
    public function viewFinancialData(User $user): bool
    {
        return (bool) $user->is_admin;
    }
}
