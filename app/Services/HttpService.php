<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class HttpService
{
    // Host consentiti per questa feature (NewsAPI)
    private array $allowedHosts = ['newsapi.org'];

    // Host bloccati sempre
    private array $blockedHosts = ['internal.finance', 'internal.admin', 'localhost', '127.0.0.1', '::1'];

    public function getRequest(string $url): ?string
    {
        $parts  = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host   = strtolower($parts['host'] ?? '');

        // Schema valido
        if (!in_array($scheme, ['http', 'https'], true)) {
            abort(400, 'Protocol not allowed');
        }

        // Blocca host interni/loopback 
        if (in_array($host, $this->blockedHosts, true)) {
            // if (!Auth::check() || !method_exists(Auth::user(), 'hasRole') || !Auth::user()->hasRole('admin')) {
            abort(403, 'Domain not allowed');
            // }
        }

        // Consenti solo host whitelisted
        if (!in_array($host, $this->allowedHosts, true)) {
            abort(403, 'Domain not allowed');
        }

        // Chiamata HTTP con timeout
        $resp = Http::timeout(5)->get($url);

        if ($resp->failed()) {
            // \Log::warning('HttpService GET failed', ['url'=>$url, 'status'=>$resp->status()]);
            return null;
        }

        return $resp->body(); // Verrà json_decode a livello del componente
    }
}
