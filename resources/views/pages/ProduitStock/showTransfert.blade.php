<div class="row mb-4">
  <div class="col-md-6">
    <div class="detail-item">
      <h6 class="text-muted">Référence</h6>
      <h4><span class="badge bg-primary fs-6">{{ $transfert->referenceTransfert }}</span></h4>
    </div>
  </div>
  <div class="col-md-6">
    <div class="detail-item">
      <h6 class="text-muted">Date du Transfert</h6>
      <h4>{{ \Carbon\Carbon::parse($transfert->dateTransfert)->format('d/m/Y') }}</h4>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-md-6">
    <div class="detail-item">
      <h6 class="text-muted">Magasin Source</h6>
      <h4><i class="fas fa-store"></i> {{ $transfert->magasinSource->libelle ?? 'Non spécifié' }}</h4>
    </div>
  </div>
  <div class="col-md-6">
    <div class="detail-item">
      <h6 class="text-muted">Magasin Destination</h6>
      <h4><i class="fas fa-store"></i> {{ $transfert->magasin->libelle }}</h4>
    </div>
  </div>
</div>

<!-- Produits transférés -->
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">
      <i class="fas fa-box"></i> Produits Transférés
      <span class="badge bg-info float-end">{{ $transfert->details->count() }} produits</span>
    </h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead class="table-light">
          <tr>
            <th>Produit</th>
            <th class="text-center">Quantité Transférée</th>
            <th class="text-center">CUMP</th>
            <th class="text-center">Valeur Totale</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($transfert->details as $detail)
          <tr>
            <td>
              <strong>{{ $detail->produit->libelle ?? 'Produit inconnu' }}</strong>
              @if($detail->produit->desc)
              <br><small class="text-muted">{{ $detail->produit->desc }}</small>
              @endif
            </td>
            <td class="text-center">
              <span class="badge bg-success fs-6">{{ $detail->qteTransferer }}</span>
            </td>
            <td class="text-center">{{ number_format($detail->produit->prix ?? 0, 2, ',', ' ') }} </td>
            <td class="text-center">
              <strong>{{ number_format(($detail->produit->prix ?? 0) * $detail->qteTransferer, 2, ',', ' ') }} </strong>
            </td>
          </tr>
          @endforeach
        </tbody>
        <tfoot class="table-light">
          <tr>
            <th colspan="3" class="text-end">Total Général:</th>
            <th class="text-center">
              {{ number_format($transfert->details->sum(function($detail) {
                return ($detail->produit->prix ?? 0) * $detail->qteTransferer;
              }), 2, ',', ' ') }} 
            </th>
          </tr>
          <tr>
            <th colspan="3" class="text-end">Total Quantité:</th>
            <th class="text-center">
              <span class="badge bg-info fs-6">{{ $transfert->details->sum('qteTransferer') }}</span>
            </th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

<!-- Informations supplémentaires -->
<div class="row mt-4">
  <div class="col-md-6">
    <div class="alert alert-info">
      <h6><i class="fas fa-info-circle"></i> Information</h6>
      <p class="mb-0">
        Ce transfert a été effectué le {{ \Carbon\Carbon::parse($transfert->dateTransfert)->format('d/m/Y à H:i') }}.
        Les produits ont été déduits du stock source et ajoutés au stock de destination.
      </p>
    </div>
  </div>
  <div class="col-md-6">
    <div class="alert alert-success">
      <h6><i class="fas fa-check-circle"></i> Statut</h6>
      <p class="mb-0">
        <strong>Transfert complété avec succès</strong><br>
        {{ $transfert->details->count() }} produits traités
      </p>
    </div>
  </div>
</div>
