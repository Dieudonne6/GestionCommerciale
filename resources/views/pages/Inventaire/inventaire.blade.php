@extends('layouts.master')
@section('content')
    <style>
        .modal-header {
            background-color: #fff !important;
        }

        .modal-title {
            color: #000 !important;
        }
    </style>
    <div class="container-xxl">
        <div class="row">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible">
                    {{ session('success') }}
                </div>
            @endif
            <div class="col-12">
                <div class="card">
                    <div class="card-header no-print">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Inventaire des Produits</h4>

                            </div><!--end col-->

                        </div><!--end row-->
                    </div><!--end card-header-->

                    <div class="row mb-3 align-items-center no-print">
                        <div class="col-md-8">
                        <form method="POST" action="{{ route('inventaires.search') }}" class="row mb-4">
                            @csrf
                            <div class="col-md-3">
                                <label>Date début</label>
                                <input type="date" name="date_debut" class="form-control" required>
                            </div>

                            <div class="col-md-3">
                                <label>Date fin</label>
                                <input type="date" name="date_fin" class="form-control" required>
                            </div>

                            <div class="col-md-3 d-flex align-items-end">
                                <button class="btn btn-primary w-100">Afficher</button>
                            </div>

       
                        </form>


                            <div class=" d-flex align-items-end">
                            @if(isset($dateDebut, $dateFin))
                                <div class="d-flex justify-content-end mb-3">
                                    <button onclick="printInventaire()" class="btn btn-secondary w-100 no-print">
                                        🖨️ Imprimer
                                    </button>
                                </div>
                            @endif
                            </div>

                        </div>
                    </div>

                    <div class="card-body pt-0">

                        {{-- @if(isset($dateDebut, $dateFin))
                            <div class="alert alert-info text-center fw-bold mb-4">
                                📊 Inventaire des produits du 
                                {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} 
                                au 
                                {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}
                            </div>
                        @endif --}}

                        {{-- <div class="table-responsive">
                            <table class="table mb-0 checkbox-all" id="datatable_1">
                                <thead class="table-light">
                                    <tr>
                                        
                                        <th class="text-center">No</th>
                                        <th class="text-center">Ref. Vente</th>
                                        <th class="text-center">Nom Client</th>
                                        <th class="text-center">Date Operation</th>
                                        <th class="text-center">Montant total</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($allFactures as $facture)
                                        <tr class="text-center">
                                            
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $facture->vente->reference }}</td>
                                            <td>{{ $facture->vente->nomClient }}</td>
                                            <td>{{ $facture->date }}</td>

                                            <td>
                                                @if (str_ends_with($facture->counter, 'FA'))
                                                    - {{ number_format($facture->montantTotalTTC, 0, ',', '.') }}
                                                @else
                                                    {{ number_format($facture->montantTotalTTC, 0, ',', '.') }}
                                                @endif
                                            </td>

                                            <td>
                                              
                                                <a href="{{ route('duplicatafacture', $facture->idFacture) }}"
                                                    class="btn btn-primary" 
                                                    style="margin-right: 24px; margin-left: 43px;">
                                                    Imprimer
                                                </a>

                                            </td>
                                        </tr>

                                    @endforeach
                                </tbody>
                            </table>
                        </div> --}}


                    <div id="zone-impression">

                            {{-- Titre --}}
                            @if(isset($dateDebut, $dateFin))
                            <h4 class="text-center my-4">
                                Inventaire des produits du 
                                {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} 
                                au 
                                {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}
                            </h4>
                            @endif
                        @if(isset($receptions))
                        <h5 class="mt-4">📦 Entrées de stock (Réceptions)</h5>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Date réception</th>
                                    <th>Qté réceptionnée</th>
                                    <th>Fournisseur</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($receptions as $rec)
                                <tr>
                                    <td>{{ $rec->detailCommandeAchat->produit->libelle }}</td>
                                    <td>{{ $rec->receptionCmdAchat->created_at->format('d/m/Y H:i:s') }}</td>
                                    {{-- <td>{{ $rec->receptionCmdAchat->date }}</td> --}}
                                    <td>{{ $rec->qteReceptionne }}</td>
                                    <td>
                                        {{ optional($rec->receptionCmdAchat->commandeAchat->fournisseur)->nom }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif



                        @if(isset($ventes))
                        <h5 class="mt-5">🧾 Sorties de stock (Ventes)</h5>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Date vente</th>
                                    <th>Qté vendue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ventes as $vente)
                                <tr>
                                    <td>{{ $vente->produit->libelle }}</td>
                                    <td>{{ $vente->vente->created_at->format('d/m/Y H:i:s') }}</td>
                                    {{-- <td>{{ $vente->vente->dateOperation }}</td> --}}
                                    <td>{{ $vente->qte }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif


                        @if(isset($recapProduits))
                            <h5 class="mt-5">📊 Récapitulatif des stocks</h5>

                            <table class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produit</th>
                                        <th>Stock initial</th>
                                        <th>Total réceptionné</th>
                                        <th>Total vendu</th>
                                        <th>Stock final</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recapProduits as $recap)
                                    <tr>
                                        <td>{{ $recap['produit'] }}</td>
                                        <td class="text-end">{{ $recap['stock_initial'] }}</td>
                                        <td class="text-end text-success">{{ $recap['receptionne'] }}</td>
                                        <td class="text-end text-danger">{{ $recap['vendu'] }}</td>
                                        <td class="text-end fw-bold">
                                            {{ $recap['stock_final'] }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif



                    </div>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div><!-- container -->

    
@endsection



 <!-- Script pour afficher le modal après actualisation si erreurs -->

 <script>
function printInventaire() {

    let contenu = document.getElementById('zone-impression').innerHTML;

    let printWindow = window.open('', '', 'height=800,width=1000');

    printWindow.document.write(`
        <html>
        <head>
            <title>Inventaire</title>

            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 15px;
                }

                table, th, td {
                    border: 1px solid #000;
                }

                th, td {
                    padding: 8px;
                    text-align: left;
                }

                h4, h5 {
                    text-align: center;
                }
            </style>
        </head>
        <body>
            ${contenu}
        </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();

    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
}
</script>


@section('styles')
<style>
    #datatable_1 td,
    #datatable_1 th {
        text-align: center;
    }

    /* Centrer les images dans les cellules */
    #datatable_1 td img {
        display: block;
        margin: 0 auto;
    }

    /* Centrer le texte dans les liens aussi */
    #datatable_1 td a {
        display: inline-block;
        text-align: center;
    }

    .modal-content {
        border-radius: 8px;
    }
</style>
    







@endsection


