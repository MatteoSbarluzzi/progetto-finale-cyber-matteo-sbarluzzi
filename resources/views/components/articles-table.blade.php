<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th scope="col">#</th>
            <th scope="col">Title</th>
            <th scope="col">Subtitle</th>
            <th scope="col">Author</th>
            <th scope="col">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($articles as $article)
            <tr>
                <th scope="row">{{$article->id}}</th>
                <td>{{$article->title}}</td>
                <td>{{$article->subtitle}}</td>
                <td>{{$article->user->name}}</td>
                <td>
                    @can('publish', $article)
                        @if (is_null($article->is_accepted))
                            <form action="{{route('revisor.acceptArticle', $article)}}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">Accept</button>
                            </form>
                            <form action="{{route('revisor.rejectArticle', $article)}}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger">Reject</button>
                            </form>
                            <a href="{{route('articles.show', $article)}}" class="btn btn-secondary">Read</a>
                        @else
                            <form action="{{route('revisor.undoArticle', $article)}}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-secondary">Back to review</button>
                            </form>
                        @endif
                    @else
                        <a href="{{route('articles.show', $article)}}" class="btn btn-secondary">Read</a>
                    @endcan
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
