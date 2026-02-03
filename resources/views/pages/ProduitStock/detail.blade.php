@extends('layouts.master')
@section('content')

<style>
@media print {
  .no-print { display: none; }
}
th{
    font-weight: bold !important;
}

/* ===== STYLE ÉCRAN ===== */
th {
    font-weight: bold !important;
}

/* ===== STYLE IMPRESSION ===== */
@media print {

  @page {
    size: A4;
    margin: 15mm;
  }

  body {
    font-size: 12px;
    margin: 0;
  }

  .no-print {
    display: none !important;
  }

  .container {
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
  }

  .card {
    border: none !important;
    box-shadow: none !important;
  }

  .card-body {
    padding: 0 !important;
  }

  table {
    width: 100% !important;
    table-layout: fixed; /* 🔥 essentiel */
  }

  th, td {
    word-wrap: break-word;
    white-space: normal !important;
    vertical-align: top;
  }

  tr {
    page-break-inside: avoid;
  }

  /* Description longue = retour à la ligne propre */
  td {
    overflow-wrap: break-word;
  }

  img {
    max-width: 100%;
    height: auto;
  }
}
</style>



<div class="container my-4" >
  <div class="card shadow">
     
    <div class="card-header d-flex justify-content-between">
        <button type="button" onclick="history.back()" class="btn btn-secondary no-print" style="">
            ← Retour
        </button>

        <h4>A propos du produit: <strong>{{ $produit->libelle }}</strong></h4>
     
        <button onclick="window.print()" class="btn btn-primary no-print">
            <i class="fa-solid fa-print"></i> Imprimer
        </button>
    </div>

    <div class="card-body" id="print-area">
      <div class="row">      

        <div class="col-12">

          <table class="table table-bordered">
            <tr>
                <th>Image</th>
                <td>   <img src="data:image/jpeg;base64,{{ base64_encode($produit->image) }}"
               class="img-fluid rounded border"
               style="max-height:250px;"></td>
            </tr>
            <tr>
              <th>Magasin</th>
              <td>{{ $produit->stocke->magasin->libelle ?? '—' }}</td>
            </tr>
            <tr>
              <th>Libellé</th>
              <td>{{ $produit->libelle }}</td>
            </tr>          
            <tr>
              <th>Famille</th>
              <td>{{ $produit->familleProduit->libelle ?? '—' }}</td>
            </tr>
            <tr>
              <th>Catégorie</th>
              <td>{{ $produit->categorieProduit->libelle ?? '—' }}</td>
            </tr>
           <tr>
              <th>Prix d’achat réel</th>
              <td>{{ number_format($produit->prixReelAchat, 0, ',', ' ') }}</td>
            </tr>
             <tr>
              <th>Prix d’achat théorique</th>
              <td>{{ number_format($produit->prixReelAchat, 0, ',', ' ') }}</td>
            </tr>
            <tr>
              <th>Prix de vente</th>
              <td>{{ number_format($produit->prix, 0, ',', ' ') }}</td>
            </tr>      
            <tr>
              <th>Stock actuel</th>
              <td>{{ $produit->stocke->qteStocke ?? 0 }}</td>
            </tr>        
            <tr>
              <th>Description</th>
              <td style="white-space: pre-line;">
                {{ $produit->desc }}
              </td>

            </tr>
          </table>

        </div>
      </div>
    </div>
  </div>
</div>

@endsection
