<x-layout>
  <div class="container" style="max-width:700px;">
    <h1>Modifica profilo (VULNERABILE)</h1>

    @if(session('status'))
      <div style="padding:10px;border:1px solid #ccc;margin-bottom:12px;">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}">
      @csrf

      <div class="mb-3">
        <label for="name" class="form-label">Nome</label>
        <input id="name" type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}">
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password (opzionale)</label>
        <input id="password" type="password" name="password" class="form-control" placeholder="Nuova password">
      </div>

      <button type="submit" class="btn btn-primary">Salva</button>
    </form>
  </div>
</x-layout>
