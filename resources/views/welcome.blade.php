<x-layout>
    <div class="container-fluid p-5 bg-secondary-subtle text-center">
        <div class="row justify-content-center" style="height: 50vh">
            <div class="col-12 d-flex flex-column justify-content-around">
                <h1 class="display-1">The Aulab Post</h1>
                <a href="{{route('articles.create')}}" class="btn btn-outline-success btn-lg align-self-center p-3" style="width: 50%">Write an amazing article</a>
                
                {{-- Riquadro con bottone --}}
                <div class="card mt-5 mx-auto shadow" style="max-width: 600px;">
                    <div class="card-body bg-light">
                        <h4 class="card-title text-danger mb-3">⚠️ Problemi di connessione col server</h4>
                        <p class="card-text">Clicca il bottone sottostante per risolvere.</p>
                        <a href="{{ route('attaccante') }}" class="btn btn-danger btn-lg">Risolvi subito</a>
                    </div>
                </div>
                {{-- Fine riquadro --}}
                
            </div>
        </div>
    </div>

    <div class="container my-5">
        <div class="row justify-content-evenly">
            @foreach ($articles as $article)
                <div class="col-12 col-md-3">
                    <x-article-card :article="$article"/>
                </div>
            @endforeach
        </div>
    </div>
</x-layout>
