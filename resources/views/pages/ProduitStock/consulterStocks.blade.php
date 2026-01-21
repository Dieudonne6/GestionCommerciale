@extends('layouts.master')
@section('content')
<style>
  .modal-header {
    background-color: #fff !important;
  }
  .modal-title {
    color: #000 !important;
  }
  .stock-low {
    background-color: #fff3cd !important;
  }
  .stock-critical {
    background-color: #f8d7da !important;
  }
  .stock-rupture {
    background-color: #ffebee !important;
    color: #d32f2f !important;
  }
  .stock-risque {
    background-color: #fff8e1 !important;
    color: #f57c00 !important;
  }
  /* .stock-critique {
    background-color: #f8d7da !important;
  } */
  .stats-card {
    transition: transform 0.2s;
  }
  .stats-card:hover {
    transform: translateY(-2px);
  }
</style>

<div class="container-xxl">
  <div class="row justify-content-center">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col">
              <h4 class="card-title">Consultation des Stocks</h4>
            </div>
            {{-- <div class="col-auto">
              <a href="{{ route('stocks.ajuster') }}" class="btn btn-warning">
                <i class="fa-solid fa-edit me-1"></i> Ajuster les Stocks
              </a>
            </div> --}}
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

        <!-- Statistiques -->
        <div class="card-body border-bottom">
          <div class="row">
            <div class="col-md-3">
              <div class="stats-card card bg-primary text-white">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                      <h5 class="mb-0" id="statTotalProduits">{{ $totalProduits }}</h5>
                      <p class="mb-0">Total Produits</p>
                    </div>
                    <div class="avatar-sm bg-light rounded-circle">
                      <i class="fas fa-box fa-2x text-primary"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stats-card card bg-success text-white">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                      <h5 class="mb-0" id="statStockTotal">{{ $stockTotal }}</h5>
                      <p class="mb-0">Stock Total</p>
                    </div>
                    <div class="avatar-sm bg-light rounded-circle">
                      <i class="fas fa-warehouse fa-2x text-success"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stats-card card bg-warning text-white">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                      <h5 class="mb-0" id="statProduitsCritique">{{ $produitsEnRupture }}</h5>
                      <p class="mb-0">Produit en Stock Critique</p>
                    </div>
                    <div class="avatar-sm bg-light rounded-circle">
                      <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="stats-card card bg-info text-white">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                      <h5 class="mb-0" id="statMagasins">{{ $magasins->count() }}</h5>
                      <p class="mb-0">Magasins</p>
                    </div>
                    <div class="avatar-sm bg-light rounded-circle">
                      <i class="fas fa-store fa-2x text-info"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

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
              <select class="form-select" id="filterStock">
                <option value="">Tous les stocks</option>
                <option value="rupture">Rupture</option>
                <option value="critical">Critique</option>
                <option value="low">Risque de rupture</option>
                <option value="normal">Normal</option>
              </select>
            </div>
            <div class="col-md-4">
              <input type="text" class="form-control" id="searchProduit" placeholder="Rechercher un produit...">
            </div>
          </div>

          <div class="table-responsive">
            <table class="table mb-0" id="stocksTable">
              <thead class="table-light">
                <tr>
                  <th class="text-center">No</th>
                  <th class="text-center">Produit</th>
                  <th class="text-center">Magasin</th>
                  <th class="text-center">Quantité</th>
                  <th class="text-center">Statut</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                @php $i = 1 @endphp
                @foreach ($stocks as $stock)
                @if($stock->produit && $stock->magasin)
                <?php
                    $statutClass = 'stock-normal';
                    $statutText  = 'Normal';
                    $statutIcon  = 'fa-check-circle text-success';
                    $stockAlert   = max(0, (int) ($stock->produit->stockAlert ?? 0));
                    $stockMinimum = max(0, (int) ($stock->produit->stockMinimum ?? 0));
                    $qteStock     = (int) $stock->qteStocke;

                    // Rupture : priorité absolue
                    if ($qteStock === 0) {
                        $statutClass = 'stock-rupture';
                        $statutText  = 'Rupture';
                        $statutIcon  = 'fa-exclamation-triangle text-danger';

                    // Critique : stock <= minimum 
                    } elseif ($stockMinimum > 0 && $qteStock <= $stockMinimum) {
                        $statutClass = 'stock-risque';
                        $statutText  = 'Risque de rupture';
                        $statutIcon  = 'fa-exclamation-triangle text-warning';

                    // Risque : stock <= alerte 
                    } elseif ($stockAlert > 0 && $qteStock <= $stockAlert) {
                        $statutClass = 'stock-critique';
                        $statutText  = 'Critique';
                        $statutIcon  = 'fa-exclamation-circle text-warning';
                    }
                ?>
                <tr class="{{ $statutClass }}" data-magasin="{{ $stock->idMag }}" data-stock-status="{{ $stock->qteStocke == 0 ? 'rupture' : ($stock->qteStocke <= $stockMinimum ? 'critical' : ($stock->qteStocke <= $stockAlert ? 'low' : 'normal')) }}" data-produit="{{ strtolower($stock->produit->libelle) }}">
                  <td class="text-center">{{ $i }}</td>
                  <td class="text-center">
                    <div class="d-flex align-items-center">
                      @if($stock->produit->image)
                      <img src="data:image/jpeg;base64,{{ base64_encode($stock->produit->image) }}" 
                           alt="Image du produit" 
                           class="rounded me-2" 
                           style="width: 40px; height: 40px; object-fit: cover;">
                      @endif
                      <div>
                        <strong>{{ $stock->produit->libelle }}</strong>
                        <br>
                        <small class="text-muted">{{ $stock->produit->prix }} F</small>
                      </div>
                    </div>
                  </td>
                  <td class="text-center">{{ $stock->magasin->libelle }}</td>
                  <td class="text-center">
                    <span class="badge bg-{{ $stock->qteStocke > 0 ? 'primary' : 'danger' }} fs-6">
                      {{ $stock->qteStocke }}
                    </span>
                  </td>
                  <td class="text-start">
                    <i class="fas {{ $statutIcon }}"></i>
                    <span class="ms-1">{{ $statutText }}</span>
                  </td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm btn-info" onclick="showStockDetails({{ $stock->idStocke }})">
                      <i class="fas fa-eye"></i> Détails
                    </button>
                  </td>
                </tr>
                @endif
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

<!-- Modal Détails du Stock -->
<div class="modal fade" id="stockDetailsModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Détails du Stock</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="stockDetailsContent">
        <!-- Le contenu sera chargé dynamiquement -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
        <a href="#" class="btn btn-warning" id="ajusterStockBtn">
          <i class="fas fa-edit"></i> Ajuster ce stock
        </a>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtrage par magasin
    document.getElementById('filterMagasin').addEventListener('change', function() {
        filterTable();
    });

    // Filtrage par statut de stock
    document.getElementById('filterStock').addEventListener('change', function() {
        filterTable();
    });
    // Recherche par produit
    document.getElementById('searchProduit').addEventListener('input', function() {
        filterTable();
    });

    function filterTable() {
        const magasinFilter = document.getElementById('filterMagasin').value;
        const stockFilter = document.getElementById('filterStock').value;
        const searchFilter = document.getElementById('searchProduit').value.toLowerCase();
        
        const rows = document.querySelectorAll('#stocksTable tbody tr');
        let visibleRows = [];
        
        rows.forEach(row => {
            let show = true;
            
            // Filtre magasin
            if (magasinFilter && row.dataset.magasin !== magasinFilter) {
                show = false;
            }
            
            // Filtre statut stock
            if (stockFilter && row.dataset.stockStatus !== stockFilter) {
                show = false;
            }
            
            // Filtre recherche
            if (searchFilter && !row.dataset.produit.includes(searchFilter)) {
                show = false;
            }
            
            row.style.display = show ? '' : 'none';
            if (show) {
                visibleRows.push(row);
            }
        });
        
        // Mettre à jour les statistiques
        updateStatistics(visibleRows);
    }
    
    function updateStatistics(visibleRows) {
        let totalProduits = visibleRows.length;
        let stockTotal = 0;
        let produitsCritique = 0;
        let magasinsCount = new Set();
        
        visibleRows.forEach(row => {
            // Extraire la quantité de stock
            const quantityElement = row.querySelector('td:nth-child(4) .badge');
            const quantity = quantityElement ? parseInt(quantityElement.textContent) || 0 : 0;
            stockTotal += quantity;
            
            // Compter les produits en stock critique
            const statusElement = row.querySelector('td:nth-child(5) span');
            if (statusElement) {
                const statusText = statusElement.textContent.trim();
                if (statusText === 'Rupture' || statusText === 'Critique' || statusText === 'Risque de rupture') {
                    produitsCritique++;
                }
            }
            
            // Compter les magasins uniques
            const magasinCell = row.querySelector('td:nth-child(3)');
            if (magasinCell) {
                magasinsCount.add(magasinCell.textContent.trim());
            }
        });
        
        // Mettre à jour l'affichage des statistiques
        document.getElementById('statTotalProduits').textContent = totalProduits;
        document.getElementById('statStockTotal').textContent = stockTotal;
        document.getElementById('statProduitsCritique').textContent = produitsCritique;
        document.getElementById('statMagasins').textContent = magasinsCount.size;
    }
});

function showStockDetails(stockId) {
    fetch(`/stock-details/${stockId}`)
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.error || 'Erreur lors du chargement');
                });
            }
            return response.json();
        })
        .then(data => {
            // Vérifier si les données requises existent
            if (!data.produit) {
                throw new Error('Produit non associé à ce stock');
            }
            
            if (!data.magasin) {
                throw new Error('Magasin non associé à ce stock');
            }
            
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informations du produit</h6>
                        <p><strong>Libellé:</strong> ${data.produit.libelle || 'N/A'}</p>
                        <p><strong>Prix:</strong> ${data.produit.prix || 0} F</p>
                        <p><strong>Description:</strong> ${data.produit.desc || 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Informations de stock</h6>
                        <p><strong>Quantité actuelle:</strong> ${data.qteStocke || 0}</p>
                        <p><strong>Seuil d'alerte:</strong> ${data.produit.stockAlert || 0}</p>
                        <p><strong>Stock minimum:</strong> ${data.produit.stockMinimum || 0}</p>
                        <p><strong>Magasin:</strong> ${data.magasin.libelle || 'N/A'}</p>
                    </div>
                </div>
                <div id="imageContainer" class="text-center mt-3">
                    ${data.produit.image ? '<p><strong>Chargement de l\'image...</strong></p>' : '<p><em>Aucune image disponible</em></p>'}
                </div>
            `;
            
            document.getElementById('stockDetailsContent').innerHTML = content;
            document.getElementById('ajusterStockBtn').href = `/ajuster-stocks#${data.idStocke}`;
            
            // Charger l'image si elle existe
            if (data.produit.image) {
                loadProductImage(data.produit.idPro);
            }
            
            const modal = new bootstrap.Modal(document.getElementById('stockDetailsModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur: ' + error.message);
        });
}

function loadProductImage(productId) {
    fetch(`/produit-image/${productId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur lors du chargement de l\'image');
            }
            return response.json();
        })
        .then(data => {
            const imageContainer = document.getElementById('imageContainer');
            imageContainer.innerHTML = `
                <img src="${data.imageSrc}" 
                     alt="Image du produit" 
                     class="rounded" 
                     style="max-width: 200px; height: auto; border: 1px solid #ddd;">
            `;
        })
        .catch(error => {
            console.error('Erreur image:', error);
            document.getElementById('imageContainer').innerHTML = '<p><em>Erreur lors du chargement de l\'image</em></p>';
        });
}
</script>
@endsection
