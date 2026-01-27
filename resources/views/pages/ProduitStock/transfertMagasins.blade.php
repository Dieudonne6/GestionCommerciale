@extends('layouts.master')

@section('content')
<style>
  .product-table tbody tr:hover {
    background-color: #f8f9fa;
  }
</style>

<div class="container-xxl">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <h4 class="card-title">Transferts entre Magasins</h4>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTransfertModal">
            <i class="fa fa-plus"></i> Nouveau Transfert
          </button>
        </div>

        @if(session('status'))
          <div class="alert alert-success m-3">{{ session('status') }}</div>
        @endif

        @if(session('erreur'))
          <div class="alert alert-danger m-3">{{ session('erreur') }}</div>
        @endif

        <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Référence</th>
              <th>Date</th>
              <th>Magasin Source</th>
              <th>Magasin Destination</th>
              <th class="text-center">Produits</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($transferts as $i => $t)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                  <span class="badge bg-primary">{{ $t->referenceTransfert }}</span>
                </td>
                <td>{{ \Carbon\Carbon::parse($t->dateTransfert)->format('d/m/Y') }}</td>
                <td>{{ $t->magasinSource->libelle ?? '-' }}</td>
                <td>{{ $t->magasin->libelle ?? '-' }}</td>
                <td class="text-center">
                  <span class="badge bg-info">{{ $t->details->count() }}</span>
                </td>
                <td class="text-center">
                  <button class="btn btn-sm btn-info"
                    onclick="showTransfertDetails({{ $t->idTransMag }})">
                    <i class="fa fa-eye"></i> Détails
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted">
                  Aucun transfert enregistré
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>


      </div>
    </div>
  </div>
</div>

<!-- MODAL TRANSFERT -->
<div class="modal fade" id="createTransfertModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <form method="POST" action="{{ route('transferts.store') }}" id="transfertForm" class="modal-content">
      @csrf

      <div class="modal-header">
        <h5 class="modal-title">Nouveau Transfert</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <!-- INFOS -->
        <div class="row mb-3">
          <div class="col-md-4">
            <label>Magasin Source *</label>
            <select class="form-select" id="idMagSource" name="idMagSource" required>
              <option value="">-- choisir --</option>
              @foreach($magasins as $m)
                <option value="{{ $m->idMag }}">{{ $m->libelle }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-4">
            <label>Magasin Destination *</label>
            <select class="form-select" name="idMagDestination" required>
              <option value="">-- choisir --</option>
              @foreach($magasins as $m)
                <option value="{{ $m->idMag }}">{{ $m->libelle }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-4">
            <label>Date *</label>
            <input type="date" name="dateTransfert" class="form-control"
                   value="{{ date('Y-m-d') }}" required>
          </div>
        </div>

        <div class="mb-3">
          <label>Motif *</label>
          <textarea name="motif" class="form-control" rows="2" required></textarea>
        </div>

        <!-- PRODUITS -->
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">Produits du magasin source</h6>
          </div>

          <div class="card-body">
            <div id="stocksContainer">
              <p class="text-muted text-center">
                Sélectionnez un magasin source
              </p>
            </div>
            <input type="hidden" name="produits" id="produitsInput">
          </div>
        </div>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button class="btn btn-success" id="submitBtn" disabled>
          <i class="fa fa-exchange-alt"></i> Effectuer le transfert
        </button>
      </div>

    </form>
  </div>
</div>

<!-- Modal de détails du transfert -->
<div class="modal fade" id="showTransfertModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Détails du Transfert</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="transfertDetailsContent">
        <div class="text-center py-4">
          <div class="spinner-border text-primary" role="status"></div>
          <p class="mt-2">Chargement des détails...</p>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          Fermer
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
    <script>
          let selectedProducts = [];

          document.getElementById('idMagSource').addEventListener('change', function () {
              const idMag = this.value;
              selectedProducts = [];
              document.getElementById('submitBtn').disabled = true;

              if (!idMag) {
                  document.getElementById('stocksContainer').innerHTML =
                    '<p class="text-muted text-center">Sélectionnez un magasin source</p>';
                  return;
              }

              fetch(`/transferts/stocks/${idMag}`)
                .then(res => res.json())
                .then(data => renderStocks(data));
          });

          function renderStocks(stocks) {
              if (stocks.length === 0) {
                  document.getElementById('stocksContainer').innerHTML =
                    '<p class="text-center text-muted">Aucun produit disponible</p>';
                  return;
              }

              document.getElementById('stocksContainer').innerHTML = `
                <table class="table product-table">
                  <thead class="table-light">
                    <tr>
                      <th></th>
                      <th>Produit</th>
                      <th class="text-center">Stock</th>
                    </tr>
                  </thead>
                  <tbody>
                    ${stocks.map(s => `
                      <tr>
                        <td class="text-center">
                          <input type="checkbox"
                            onchange="toggleProduct(${s.idStocke}, '${s.libelle}', this)">
                        </td>
                        <td>${s.libelle}</td>
                        <td class="text-center">
                          <span class="badge bg-info">${s.qteStocke}</span>
                        </td>
                      </tr>
                    `).join('')}
                  </tbody>
                </table>`;
          }

          function toggleProduct(idStocke, libelle, el) {
              if (el.checked) {
                  selectedProducts.push({ idStocke, libelle });
              } else {
                  selectedProducts = selectedProducts.filter(p => p.idStocke !== idStocke);
              }
              document.getElementById('submitBtn').disabled = selectedProducts.length === 0;
          }

          document.getElementById('transfertForm').addEventListener('submit', function (e) {
              if (selectedProducts.length === 0) {
                  e.preventDefault();
                  alert('Sélectionnez au moins un produit');
                  return;
              }
              document.getElementById('produitsInput').value =
                JSON.stringify(selectedProducts);
          });

          function showTransfertDetails(idTransMag) {
              const modal = new bootstrap.Modal(
                  document.getElementById('showTransfertModal')
              );
              modal.show();

              fetch(`/transferts/${idTransMag}/details`)
                  .then(res => res.text())
                  .then(html => {
                      document.getElementById('transfertDetailsContent').innerHTML = html;
                  })
                  .catch(() => {
                      document.getElementById('transfertDetailsContent').innerHTML =
                        '<div class="alert alert-danger">Erreur de chargement</div>';
                  });
          }

    </script>
@endpush
