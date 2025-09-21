<div>
    <h3>Articles suggestions for you, get inspired!</h3>

    <form wire:submit="fetchNews">
        <label for="apiSelect">Breaking news around the world</label>
        <div class="d-flex">
            {{-- Niente URL, solo codici paese --}}
            <select wire:model="selectedCountry" id="apiSelect" class="form-select">
                <option value="">Choose country</option>
                <option value="it">NewsAPI - IT</option>
                <option value="gb">NewsAPI - UK</option>
                <option value="us">NewsAPI - US</option>
            </select>
            <button type="submit" class="btn btn-info">Go</button>
        </div>
    </form>

    <div class="mt-3">
        @if(isset($news['error']))
            <p class="text-danger">{{ $news['error'] }}</p>
        @elseif(isset($news['articles']) && is_array($news['articles']))
            @forelse($news['articles'] as $article)
                <div class="news-article mb-3 p-2 border rounded">
                    <h4 class="h6">{{ $article['title'] ?? 'Untitled' }}</h4>
                    <p>{{ $article['description'] ?? '' }}</p>
                    @if(!empty($article['url']))
                        <a href="{{ $article['url'] }}" target="_blank" rel="noopener">Read more</a>
                    @endif
                </div>
            @empty
                <h3>No articles around you</h3>
            @endforelse
        @endif
    </div>
</div>
