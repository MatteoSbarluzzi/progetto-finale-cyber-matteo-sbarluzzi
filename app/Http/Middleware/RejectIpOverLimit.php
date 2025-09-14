<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RejectIpOverLimit
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        // Se l'IP è stato segnalato come "over limit" bloccalo
        if (Cache::has("ip-over-limit:$ip")) {
            return response()->json([
                'message' => 'Il tuo IP è temporaneamente bloccato per troppi tentativi.'
            ], 429);
        }

        return $next($request);
    }
}
