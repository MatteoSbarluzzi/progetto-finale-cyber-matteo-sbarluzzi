<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Validator;
use App\Services\HttpService;

class LatestNews extends Component
{
    // Niente URL dal client
    public string $selectedCountry = '';
    public array $news = [];

    // Whitelist di valori accettati dal client
    private array $allowedCountries = ['it', 'gb', 'us'];

    public function fetchNews()
    {
        // Validazione lato server; accetta solo questi paesi
        Validator::validate(
            ['selectedCountry' => $this->selectedCountry],
            ['selectedCountry' => ['required', 'in:' . implode(',', $this->allowedCountries)]],
            ['selectedCountry.in' => 'Fonte non valida']
        );

        // Costruzione URL solo lato server (chiave letta da .env)
        $url = sprintf(
            'https://newsapi.org/v2/top-headlines?country=%s&apiKey=%s',
            $this->selectedCountry,
            env('NEWSAPI_KEY')
        );

        // Chiamata tramite service che fa ulteriori controlli/whitelist host
        /** @var HttpService $http */
        $http = app(HttpService::class);

        $raw = $http->getRequest($url);      // può restituire string JSON
        $data = is_array($raw) ? $raw : json_decode($raw ?? '[]', true);

        $this->news = is_array($data) ? $data : [];
    }

    public function render()
    {
        // Passo la whitelist alla vista (comodo per generare la select)
        return view('livewire.latest-news', [
            'allowedCountries' => $this->allowedCountries,
        ]);
    }
}
