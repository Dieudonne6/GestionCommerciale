@extends('layouts.master')
@section('content')
<style>
  .modal-header {
    background-color: #fff !important;
  }
  .modal-title {
    color: #000 !important;
  }
  .transfert-row {
    transition: all 0.3s ease;
  }
  .transfert-row:hover {
    background-color: #f8f9fa;
  }
  .product-item {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-bottom: 0.5rem;
    background-color: #f8f9fa;
  }
  .product-item:hover {
    background-color: #e9ecef;
  }
  .detail-item {
    border-left: 4px solid #007bff;
    padding-left: 1rem;
    margin-bottom: 1rem;
  }
  .stock-info {
    font-size: 0.875rem;
    color: #6c757d;
  }
  .remove-product {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
  }
</style>

<div class="container-xxl">
  <div class="row justify-content-center">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col">
              <h4 class="card-title">Transferts entre Magasins</h4>
            </div>
            <div class="col-auto">
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTransfertModal">
                <i class="fa-solid fa-plus me-1"></i> Nouveau Transfert
              </button>
            </div>
          </div>
        </div>

        @if (Session::has('status'))
        <div class="alert alert-success alert-dismissible m-3">
          {{Session::get('status')}}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if (Session::has('erreur'))
        <div class="alert alert-danger alert-dismissible m-3">
          {{Session::get('erreur')}}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="card-body pt-0">
          <!-- Filtres -->
          <div class="row mb-3">
            <div class="col-md-4">
              <select class="form-select" id="filterMagasin">
                <option value="">Tous les magasins</option>
                @foreach ($magasins as $magasin)
                <option value="{{ $magasin->idMag }}">{{ $magasin->libelle }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <input type="date" class="form-control" id="filterDate" placeholder="Filtrer par date">
            </div>
            <div class="col-md-4">
              <input type="text" class="form-control" id="searchReference" placeholder="Référence...">
            </div>
          </div>

          <div class="table-responsive">
            <table class="table mb-0" id="transfertsTable">
              <thead class="table-light">
                <tr>
                  <th class="text-center">No</th>
                  <th class="text-center">Référence</th>
                  <th class="text-center">Date</th>
                  <th class="text-center">Magasin Source</th>
                  <th class="text-center">Magasin Destination</th>
                  <th class="text-center">Produits</th>
                  <th class="text-center">Total Quantité</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                @php $i = 1 @endphp
                @foreach ($transferts as $transfert)
                <tr class="transfert-row" data-magasin="{{ $transfert->idMag }}" data-date="{{ \Carbon\Carbon::parse($transfert->dateTransfert)->format('Y-m-d') }}" data-reference="{{ $transfert->referenceTransfert }}">
                  <td class="text-center">{{ $i }}</td>
                  <td class="text-center">
                    <span class="badge bg-primary">{{ $transfert->referenceTransfert }}</span>
                  </td>
                  <td class="text-center">{{ \Carbon\Carbon::parse($transfert->dateTransfert)->format('d/m/Y') }}</td>
                  <td class="text-center">{{ $transfert->magasinSource->libelle ?? 'Non spécifié' }}</td>
                  <td class="text-center">{{ $transfert->magasin->libelle }}</td>
                  <td class="text-center">{{ $transfert->details->count() }}</td>
                  <td class="text-center">
                    <span class="badge bg-info">{{ $transfert->details->sum('qteTransferer') }}</span>
                  </td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm btn-info" onclick="showTransfertDetails({{ $transfert->idTransMag }})">
                      <i class="fas fa-eye"></i> Détails
                    </button>
                  </td>
                </tr>
                @php $i++ @endphp
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal de création de transfert -->
<div class="modal fade" id="createTransfertModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nouveau Transfert entre Magasins</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('transferts.store') }}" method="POST" id="transfertForm">
        @csrf
        <div class="modal-body">
          @if (Session::has('erreur'))
          <div class="alert alert-danger alert-dismissible">
            {{Session::get('erreur')}}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
          @endif
          
          <div class="row mb-3">
            <div class="col-md-4">
              <label for="idMagSource" class="form-label">Magasin Source *</label>
              <select class="form-select" id="idMagSource" name="idMagSource" required>
                <option value="">Sélectionner le magasin source</option>
                @foreach ($magasins as $magasin)
                <option value="{{ $magasin->idMag }}">{{ $magasin->libelle }}</option>
                @endforeach
              </select>
            </div>
            
            <div class="col-md-4">
              <label for="idMagDestination" class="form-label">Magasin Destination *</label>
              <select class="form-select" id="idMagDestination" name="idMagDestination" required>
                <option value="">Sélectionner le magasin destination</option>
                @foreach ($magasins as $magasin)
                <option value="{{ $magasin->idMag }}">{{ $magasin->libelle }}</option>
                @endforeach
              </select>
            </div>
            
            <div class="col-md-4">
              <label for="dateTransfert" class="form-label">Date du Transfert *</label>
              <input type="date" class="form-control" id="dateTransfert" name="dateTransfert" value="{{ date('Y-m-d') }}" required>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-12">
              <label for="motif" class="form-label">Motif du Transfert *</label>
              <textarea class="form-control" id="motif" name="motif" rows="2" required placeholder="Ex: Réapprovisionnement, Équilibrage des stocks, Demande client..."></textarea>
            </div>
          </div>

          <!-- Section Produits -->
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h6 class="mb-0">Produits à Transférer</h6>
              <button type="button" class="btn btn-sm btn-primary" onclick="showProductModal()">
                <i class="fas fa-plus"></i> Ajouter un produit
              </button>
            </div>
            <div class="card-body">
              <div id="productsList">
                <div class="text-center text-muted py-3" id="emptyMessage">
                  <i class="fas fa-box fa-2x mb-2"></i>
                  <p>Aucun produit ajouté. Cliquez sur "Ajouter un produit" pour commencer.</p>
                </div>
              </div>
              
              <input type="hidden" name="produits" id="produitsInput" value="">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-success" id="submitBtn" disabled>
            <i class="fas fa-exchange-alt"></i> Effectuer le Transfert
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal de sélection de produit -->
<div class="modal fade" id="productModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Sélectionner un produit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info">
          <i class="fas fa-info-circle"></i> Sélectionnez un produit disponible dans le magasin source
        </div>
        
        <div class="table-responsive">
          <table class="table" id="stocksTable">
            <thead class="table-light">
              <tr>
                <th>Produit</th>
                <th class="text-center">Stock Disponible</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody id="stocksTableBody">
              <tr>
                <td colspan="4" class="text-center text-muted">
                  Veuillez d'abord sélectionner un magasin source
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
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
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
          </div>
          <p class="mt-2">Chargement des détails...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
let selectedProducts = [];

document.addEventListener('DOMContentLoaded', function() {
    // Filtrage par magasin
    document.getElementById('filterMagasin').addEventListener('change', function() {
        filterTable();
    });

    // Filtrage par date
    document.getElementById('filterDate').addEventListener('change', function() {
        filterTable();
    });

    // Recherche par référence
    document.getElementById('searchReference').addEventListener('input', function() {
        filterTable();
    });

    // Validation des magasins dans le modal de création
    document.getElementById('idMagSource').addEventListener('change', validateMagasins);
    document.getElementById('idMagDestination').addEventListener('change', validateMagasins);
    
    // Validation du formulaire de transfert
    document.getElementById('transfertForm').addEventListener('submit', function(e) {
        if (selectedProducts.length === 0) {
            e.preventDefault();
            alert('Veuillez ajouter au moins un produit au transfert.');
            return false;
        }
        
        // Mettre à jour le champ caché des produits
        document.getElementById('produitsInput').value = JSON.stringify(selectedProducts);
    });

    // Réinitialiser les produits quand le modal se ferme
    document.getElementById('createTransfertModal').addEventListener('hidden.bs.modal', function () {
        selectedProducts = [];
        updateProductsList();
        document.getElementById('transfertForm').reset();
        document.getElementById('submitBtn').disabled = true;
    });
});

function filterTable() {
    const magasinFilter = document.getElementById('filterMagasin').value;
    const dateFilter = document.getElementById('filterDate').value;
    const referenceFilter = document.getElementById('searchReference').value.toLowerCase();
    
    const rows = document.querySelectorAll('#transfertsTable tbody tr');
    
    rows.forEach(row => {
        let show = true;
        
        // Filtre magasin
        if (magasinFilter && row.dataset.magasin !== magasinFilter) {
            show = false;
        }
        
        // Filtre date
        if (dateFilter && row.dataset.date !== dateFilter) {
            show = false;
        }
        
        // Filtre référence
        if (referenceFilter && !row.dataset.reference.toLowerCase().includes(referenceFilter)) {
            show = false;
        }
        
        row.style.display = show ? '' : 'none';
    });
}

function validateMagasins() {
    const source = document.getElementById('idMagSource').value;
    const destination = document.getElementById('idMagDestination').value;
    
    if (source && destination && source === destination) {
        document.getElementById('idMagDestination').setCustomValidity('Le magasin destination doit être différent du magasin source.');
    } else {
        document.getElementById('idMagDestination').setCustomValidity('');
    }
}

function showProductModal() {
    const idMagSource = document.getElementById('idMagSource').value;
    
    if (!idMagSource) {
        alert('Veuillez d\'abord sélectionner un magasin source.');
        return;
    }
    
    // Charger les stocks du magasin source
    fetch(`/transferts/stocks/${idMagSource}`)
        .then(response => response.json())
        .then(data => {
            displayStocksTable(data);
            const modal = new bootstrap.Modal(document.getElementById('productModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du chargement des stocks.');
        });
}

function displayStocksTable(stocks) {
    const tbody = document.getElementById('stocksTableBody');

    if (stocks.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="3" class="text-center">Aucun stock</td>
          </tr>`;
        return;
    }

    tbody.innerHTML = stocks.map(stock => `
      <tr>
        <td><strong>${stock.libelle}</strong></td>
        <td class="text-center">
          <span class="badge bg-info">${stock.qteStocke}</span>
        </td>
        <td class="text-center">
          <button class="btn btn-success btn-sm"
            onclick="addProductToTransfer(${stock.idStocke}, '${stock.libelle}')">
            Transférer
          </button>
        </td>
      </tr>
    `).join('');
}


function addProductToTransfer(idStocke, libelle) {

    if (selectedProducts.find(p => p.idStocke === idStocke)) {
        alert('Produit déjà ajouté');
        return;
    }

    selectedProducts.push({
        idStocke: idStocke,
        libelle: libelle
    });

    updateProductsList();

    const modal = bootstrap.Modal.getInstance(
        document.getElementById('productModal')
    );
    modal.hide();

    document.getElementById('submitBtn').disabled = false;
}

function updateProductsList() {
    const container = document.getElementById('productsList');

    if (selectedProducts.length === 0) {
        container.innerHTML = `<p class="text-muted text-center">Aucun produit</p>`;
        document.getElementById('submitBtn').disabled = true;
        return;
    }

    container.innerHTML = selectedProducts.map((p, i) => `
      <div class="product-item">
        <button class="btn btn-danger btn-sm remove-product"
          onclick="removeProduct(${i})">✕</button>
        <strong>${p.libelle}</strong>
      </div>
    `).join('');
}


function removeProduct(index) {
    selectedProducts.splice(index, 1);
    updateProductsList();
}

function updateProductQuantity(index, newQuantity) {
    const quantity = parseInt(newQuantity);
    const product = selectedProducts[index];
    
    if (!quantity || quantity <= 0) {
        alert('Quantité invalide.');
        updateProductsList();
        return;
    }
    
    if (quantity > product.stockDisponible) {
        alert(`Quantité supérieure au stock disponible (${product.stockDisponible}).`);
        updateProductsList();
        return;
    }
    
    selectedProducts[index].quantite = quantity;
    updateProductsList();
}

function showTransfertDetails(idTransMag) {
    // Afficher le modal avec un indicateur de chargement
    const modal = new bootstrap.Modal(document.getElementById('showTransfertModal'));
    modal.show();
    
    // Charger les détails du transfert
    fetch(`/transferts/${idTransMag}/details`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('transfertDetailsContent').innerHTML = html;
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.getElementById('transfertDetailsContent').innerHTML = 
                '<div class="alert alert-danger">Erreur lors du chargement des détails</div>';
        });
}
</script>
@endpush
