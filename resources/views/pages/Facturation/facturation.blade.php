@extends('layouts.master')
@section('content')
<!-- Page Content-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Liste des facturations</h4>
                        </div><!--end col-->
                    </div> <!--end row-->
                </div><!--end card-header-->
                
                @if(Session::has('status'))
                    <div id="statusAlert" class="alert alert-success">
                        {{ Session::get('status') }}
                    </div>
                @endif
                @if(Session::has('erreur'))
                    <div id="statusAlert" class="alert alert-danger">
                        {{ Session::get('erreur') }}
                    </div>
                @endif
                
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table datatable" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th>N° Facture</th>
                                    <th>CODEMECEF</th>                                  
                                    <th>Montant Total</th>                                    
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allFactures as $facture)
                                    <tr>
                                        <td>{{ $facture->idFacture }}</td>                                        
                                        <td>{{ $facture->CODEMECEF }}</td>                                        
                                        <td>{{ number_format($facture->montantTotal, 2) }} FCFA</td>
                                        <td>{{ \Carbon\Carbon::parse($facture->date)->format('d/m/Y') }}</td>
                                        <td>
                                            <button type="button" class="btn btn-success" onclick="window.open('{{ route('factures.print', ['idFacture' => $facture->idFacture]) }}')">
                                                <i class="fa-solid fa-print me-1"></i> Imprimer
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div><!--end card-body-->
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->
</div>

@endsection