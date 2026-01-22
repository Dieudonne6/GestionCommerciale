@extends('layouts.master')

@section('content')
<div class="container my-4" id="print-area">

    {{-- Message --}}
    @if(session('error'))
        <div class="alert alert-danger no-print">
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Bouton Imprimer --}}
    <div class="d-flex justify-content-end mb-3 no-print">
        <button onclick="window.print()" class="btn btn-primary btn-sm no-print">
            <i class="iconoir-printer"></i> Imprimer le récapitulatif
        </button>
    </div>
    <style>
        @media print {
            .no-print {
                display: none !important;
                visibility: hidden !important;
            }
        }
    </style>
    @php
        // Totaux
        $totalQteProduits = collect($recapProduits)->sum('qte');
        $totalMontantProduits = collect($recapProduits)->sum('montant');
        $totalMontantVentes = collect($ventes)->sum('montantTotal');
    @endphp

    {{-- Récap produits --}}
    <div class="card shadow-lg mt-4">
        <div class="card-header bg-light d-flex justify-content-between" >
            <strong style="font-size: 1rem;">Récapitulatif des produits vendus</strong>
            <span class="badge bg-secondary" style="font-size: 1rem;">
              Clôture de la journée du {{ now()->format('d/m/Y H:i') }}
            </span>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fw-bold">Produit</th>
                        <th class="text-center fw-bold" >Qté vendue</th>
                        <th class="text-end fw-bold">Montant total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recapProduits as $produit)
                    <tr>
                        <td>{{ $produit['produit'] }}</td>
                        <td class="text-center">{{ $produit['qte'] }}</td>
                        <td class="text-end">{{ number_format($produit['montant'], 0, ',', ' ') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td class="text-end fw-bold">TOTAL</td>
                        <td class="text-center fw-bold">{{ $totalQteProduits }}</td>
                        <td class="text-end fw-bold">{{ number_format($totalMontantProduits, 0, ',', ' ') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Détail des ventes --}}
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-light">
            <strong style="font-size: 1rem;">Détail des ventes par client</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th><strong> Client / Référence</strong></th>
                        <th class="text-center fw-bold">Produits</th>
                        <th class="text-end fw-bold" >Montant</th>
                        <th class="text-center detail fw-bold">Détail</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventes as $vente)
                    <tr>
                        <td>{{ $vente['reference'] }} – {{ $vente['nomClient'] }}</td>
                        <td class="text-center">{{ $vente['nbreProduits'] }}</td>
                        <td class="text-end">{{ number_format($vente['montantTotal'], 0, ',', ' ') }}</td>
                        <td class="text-center detail">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#vente{{ $vente['id'] }}">
                                <i class="iconoir-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="3" class="text-end"><strong>TOTAL GÉNÉRAL</strong></td>
                        <td class="text-end"><strong>{{ number_format($totalMontantVentes, 0, ',', ' ') }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

{{-- MODALS --}}
@foreach($ventes as $vente)
<div class="modal fade" id="vente{{ $vente['id'] }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">{{ $vente['reference'] }} / {{ $vente['nomClient'] }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Produit</th>
                            <th class="text-center">Qté</th>
                            <th class="text-end">Montant TTC</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vente['details'] as $detail)
                        <tr>
                            <td>{{ $detail->produit->libelle }}</td>
                            <td class="text-center">{{ $detail->qte }}</td>
                            <td class="text-end">{{ number_format($detail->montantTTC, 0, ',', ' ') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@section('styles')
<style>
    h5, .card-header { font-size: 25px; }
    table th, table td { vertical-align: middle; }
    table tfoot tr td { font-weight: bold; }

    @media print {
        .no-print,
        .modal,
        .btn,
        nav,
        footer,
        .detail {
            display: none !important;
        }
        
        body { font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
    }
</style>
@endsection
@endsection
