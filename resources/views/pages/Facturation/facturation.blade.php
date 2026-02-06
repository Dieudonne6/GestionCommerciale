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
                                <h4 class="card-title">Duplicata des factures</h4>

                            </div><!--end col-->

                        </div><!--end row-->
                    </div><!--end card-header-->

                    <div class="row mb-3 align-items-center">
                        <div class="col-md-4">
                        <form method="GET" action="{{ route('facturation') }}" class="mb-3">
                            <select name="type" class="form-select w-auto" onchange="this.form.submit()">
                                <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Toutes les factures</option>
                                <option value="FV" {{ $type === 'FV' ? 'selected' : '' }}>Factures de vente</option>
                                <option value="FA" {{ $type === 'FA' ? 'selected' : '' }}>Factures d'avoir</option>
                            </select>
                        </form>
                        </div>
                    </div>

                    <div class="card-body pt-0">

                        <div class="table-responsive">
                            <table class="table mb-0 checkbox-all" id="datatable_1">
                                <thead class="table-light">
                                    <tr>
                                        
                                        <th class="text-center">No</th>
                                        <th class="text-center">Ref. Vente</th>
                                        <th class="text-center">Nom Client</th>
                                        <th class="text-center">Date Operation</th>
                                        <th class="text-center">Montant total</th>
                                        <th class="text-center">Montant aib</th>
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

                                            @if ($facture->vente->montant_aib > 0)    
                                                <td>
                                                    @if (str_ends_with($facture->counter, 'FA'))
                                                        - {{ number_format($facture->vente->montant_aib, 0, ',', '.') }}
                                                    @else
                                                        {{ number_format($facture->vente->montant_aib, 0, ',', '.') }}
                                                    @endif
                                                </td>
                                            @else
                                            <td></td>
                                            @endif


                                            {{-- <td>{{ $facture->montantTotalTTC }}</td> --}}
                                            <td>
                                                <!-- Bouton pour modifier (ouvre un modal) -->
                                                <!-- Bouton pour modifier -->
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
                        </div>
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


