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
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Inventaire des Produits</h4>

                            </div><!--end col-->

                        </div><!--end row-->
                    </div><!--end card-header-->

                    <div class="row mb-3 align-items-center">
                        <div class="col-md-4">
                        <form method="POST" action="{{ route('inventaires.search') }}" class="row mb-4">
                            @csrf
                            <div class="col-md-4">
                                <label>Date début</label>
                                <input type="date" name="date_debut" class="form-control" required>
                            </div>

                            <div class="col-md-4">
                                <label>Date fin</label>
                                <input type="date" name="date_fin" class="form-control" required>
                            </div>

                            <div class="col-md-4 d-flex align-items-end">
                                <button class="btn btn-primary w-100">Afficher</button>
                            </div>
                        </form>

                        </div>
                    </div>

                    <div class="card-body pt-0">

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


                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div><!-- container -->

    
@endsection



 <!-- Script pour afficher le modal après actualisation si erreurs -->



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


