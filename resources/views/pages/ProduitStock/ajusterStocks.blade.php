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
  .stock-critique {
    background-color: #f8d7da !important;
  }
  .ajustment-row {
    transition: all 0.3s ease;
  }
  .ajustment-row:hover {
    background-color: #f8f9fa;
  }
  .btn-ajustment {
    min-width: 120px;
  }
</style>

<div class="container-xxl">
  <div class="row justify-content-center">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col">
              <h4 class="card-title">Ajustement des Stocks</h4>
            </div>
            <div class="col-auto">
              <a href="{{ route('consulterStocks') }}" class="btn btn-primary">
                <i class="fa-solid fa-eye me-1"></i> Consulter les Stocks
              </a>
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
                  <th class="text-center">Stock Actuel</th>
                  <th class="text-center">Seuil Alert</th>
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
                <tr class="ajustment-row {{ $statutClass }}" data-magasin="{{ $stock->idMag }}" data-stock-status="{{ $stock->qteStocke == 0 ? 'rupture' : ($stock->qteStocke <= $stockMinimum ? 'critical' : ($stock->qteStocke <= $stockAlert ? 'low' : 'normal')) }}" data-produit="{{ strtolower($stock->produit->libelle) }}">
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
                    <span class="badge bg-{{ $stock->qteStocke > 0 ? 'primary' : 'danger' }} fs-6" id="stock-{{ $stock->idStocke }}">
                      {{ $stock->qteStocke }}
                    </span>
                  </td>
                  <td class="text-center">{{ $stockAlert }}</td>
                  <td class="text-center">
                    <i class="fas {{ $statutIcon }}"></i>
                    <span class="ms-1">{{ $statutText }}</span>
                  </td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm btn-success btn-ajustment" onclick="showAjustmentModal({{ $stock->idStocke }}, 'entree')">
                      <i class="fas fa-plus"></i> Entrée
                    </button>
                    <button type="button" class="btn btn-sm btn-danger btn-ajustment" onclick="showAjustmentModal({{ $stock->idStocke }}, 'sortie')" {{ $stock->qteStocke <= 0 ? 'disabled' : '' }}>
                      <i class="fas fa-minus"></i> Sortie
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

<!-- Modal d'Ajustement de Stock -->
<div class="modal fade" id="ajustmentModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ajustmentModalTitle">Ajustement de Stock</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('stocks.ajuster.stock') }}" method="POST">
        @csrf
        <input type="hidden" name="idStocke" id="ajustmentStockId">
        <input type="hidden" name="typeAjustement" id="ajustmentType">
        
        <div class="modal-body">
          <div class="alert alert-info" id="ajustmentInfo">
            <!-- Informations seront ajoutées dynamiquement -->
          </div>
          
          <div class="mb-3">
            <label for="quantite" class="form-label">Quantité à ajuster</label>
            <input type="number" class="form-control" id="quantite" name="quantite" min="1" required>
            <div class="form-text">Indiquez la quantité à {{ 'ajouter' }} au stock</div>
          </div>
          
          <div class="mb-3">
            <label for="motif" class="form-label">Motif de l'ajustement</label>
            <textarea class="form-control" id="motif" name="motif" rows="3" required placeholder="Ex: Retour client, Erreur de comptage, Nouvel approvisionnement..."></textarea>
          </div>
          
          <div class="mb-3">
            <div class="d-flex justify-content-between">
              <span><strong>Stock actuel:</strong> <span id="currentStock">0</span></span>
              <span><strong>Nouveau stock:</strong> <span id="newStock">0</span></span>
            </div>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary" id="confirmAjustment">
            Confirmer l'ajustement
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
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

    // Calcul du nouveau stock en temps réel
    document.getElementById('quantite').addEventListener('input', function() {
        calculateNewStock();
    });

    function filterTable() {
        const magasinFilter = document.getElementById('filterMagasin').value;
        const stockFilter = document.getElementById('filterStock').value;
        const searchFilter = document.getElementById('searchProduit').value.toLowerCase();
        
        const rows = document.querySelectorAll('#stocksTable tbody tr');
        
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
        });
    }

    function calculateNewStock() {
        const currentStock = parseInt(document.getElementById('currentStock').textContent);
        const quantite = parseInt(document.getElementById('quantite').value) || 0;
        const type = document.getElementById('ajustmentType').value;
        
        let newStock;
        if (type === 'entree') {
            newStock = currentStock + quantite;
        } else {
            newStock = currentStock - quantite;
        }
        
        document.getElementById('newStock').textContent = newStock;
        
        // Validation
        const confirmBtn = document.getElementById('confirmAjustment');
        if (type === 'sortie' && quantite > currentStock) {
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Quantité insuffisante';
        } else {
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Confirmer l\'ajustement';
        }
    }
});

function showAjustmentModal(stockId, type) {
    console.log('showAjustmentModal called with stockId:', stockId, 'type:', type);
    
    // Récupérer les informations du stock depuis la ligne du tableau
    const stockBadge = document.querySelector('#stock-' + stockId);
    console.log('stockBadge:', stockBadge);
    
    if (!stockBadge) {
        console.error('Stock badge not found for ID:', stockId);
        alert('Erreur: Badge de stock non trouvé pour l\'ID ' + stockId);
        return;
    }
    
    const row = stockBadge.closest('tr');
    console.log('row:', row);
    
    if (!row) {
        console.error('Row not found for stock ID:', stockId);
        alert('Erreur: Ligne de tableau non trouvée pour l\'ID ' + stockId);
        return;
    }
    
    const produitElement = row.querySelector('strong');
    const produitName = produitElement ? produitElement.textContent : 'Produit inconnu';
    
    const cells = row.querySelectorAll('td');
    const magasinName = cells.length > 2 ? cells[2].textContent : 'Magasin inconnu';
    const currentStock = parseInt(stockBadge.textContent) || 0;
    
    // Remplir les champs du modal
    document.getElementById('ajustmentStockId').value = stockId;
    document.getElementById('ajustmentType').value = type;
    document.getElementById('currentStock').textContent = currentStock;
    document.getElementById('quantite').value = '';
    document.getElementById('newStock').textContent = currentStock;
    
    // Adapter le modal selon le type d'ajustement
    const modalTitle = document.getElementById('ajustmentModalTitle');
    const ajustmentInfo = document.getElementById('ajustmentInfo');
    const quantiteLabel = document.querySelector('label[for="quantite"]');
    const quantiteHelp = document.querySelector('.form-text');
    const confirmBtn = document.getElementById('confirmAjustment');
    
    if (type === 'entree') {
        modalTitle.textContent = 'Entrée de Stock';
        ajustmentInfo.innerHTML = 
            '<i class="fas fa-info-circle"></i> ' +
            '<strong>Entrée de stock pour:</strong> ' + produitName + '<br>' +
            '<strong>Magasin:</strong> ' + magasinName + '<br>' +
            '<strong>Stock actuel:</strong> ' + currentStock;
        quantiteLabel.textContent = 'Quantité à entrer';
        quantiteHelp.textContent = 'Indiquez la quantité à ajouter au stock';
        confirmBtn.className = 'btn btn-success';
        confirmBtn.textContent = 'Confirmer l\'entrée';
    } else {
        modalTitle.textContent = 'Sortie de Stock';
        ajustmentInfo.innerHTML = 
            '<i class="fas fa-exclamation-triangle"></i> ' +
            '<strong>Sortie de stock pour:</strong> ' + produitName + '<br>' +
            '<strong>Magasin:</strong> ' + magasinName + '<br>' +
            '<strong>Stock actuel:</strong> ' + currentStock;
        quantiteLabel.textContent = 'Quantité à sortir';
        quantiteHelp.textContent = 'Indiquez la quantité à retirer du stock';
        confirmBtn.className = 'btn btn-danger';
        confirmBtn.textContent = 'Confirmer la sortie';
    }
    
    // Afficher le modal
    console.log('Tentative d\'ouverture du modal...');
    const modalElement = document.getElementById('ajustmentModal');
    console.log('modalElement:', modalElement);
    
    if (!modalElement) {
        console.error('Modal element not found');
        alert('Erreur: Modal non trouvé dans la page');
        return;
    }
    
    try {
        const modal = new bootstrap.Modal(modalElement);
        console.log('Modal instance created:', modal);
        modal.show();
        console.log('Modal show() called');
    } catch (error) {
        console.error('Error creating/showing modal:', error);
        alert('Erreur lors de l\'ouverture du modal: ' + error.message);
    }
    
    // Focus sur le champ quantité
    setTimeout(() => {
        document.getElementById('quantite').focus();
    }, 500);
}
</script>
@endpush
