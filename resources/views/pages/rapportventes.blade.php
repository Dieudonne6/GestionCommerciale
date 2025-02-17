@extends('layouts.master')

@section('content')
<div class="container mt-4">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Rapport des Ventes</h4>
            <small class="text-muted">Période : {{ request('dateOperation') ?? 'Toutes' }}</small>
        </div>
        <div class="card-body">

            <!-- Résumé des ventes -->
            <div class="mb-4">
                <h5>Résumé</h5>
                <ul>
                    <li>Total des ventes : <strong>{{ number_format($ventes->sum('montantTTC'), 2, ',', ' ') }} FCFA</strong></li>
                    <li>Nombre total de transactions : <strong>{{ $ventes->count() }}</strong></li>
                </ul>
            </div>

            <!-- Formulaire de filtrage -->
            <form method="GET" action="{{ route('rapportventes') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <label for="numV">Numéro de Vente</label>
                        <input type="text" name="numV" class="form-control" placeholder="Numéro de vente" value="{{ request('numV') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="dateOperation">Date d'Opération</label>
                        <input type="date" name="dateOperation" class="form-control" value="{{ request('dateOperation') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filtrer
                        </button>
                        <a href="{{ route('rapportventes') }}" class="ml-2 btn btn-danger">
                            <i class="fas fa-sync-alt"></i> Réinitialiser
                        </a>
                    </div>
                </div>
            </form>

            <!-- Tableau des résultats -->
            <div class="table-responsive">
                <table class="table mb-0 checkbox-all" id="datatable_1">
                    <thead class="table-light">
                        <tr>
                            <th>Numéro Vente</th>
                            <th>Quantité</th>
                            <th>Mode de Paiement</th>
                            <th>Montant TTC</th>
                            <th>Date Opération</th>
                            <th>Client</th>
                            <th>Vendeur</th>
                            <th>Produits</th> <!-- Nouvelle colonne pour afficher les produits -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ventes as $vente)
                            <tr>
                                <td>{{ $vente->numV }}</td>
                                @foreach ($query2 as $qte)
                                <td>{{ $qte->qteLVente }}</td>
                                @endforeach
                                <td>{{ $vente->modePaiement }}</td>
                                <td>{{ number_format($vente->montantTTC, 2, ',', ' ') }} FCFA</td>
                                <td>{{ \Carbon\Carbon::parse($vente->dateOperation)->format('d/m/Y') }}</td>
                                <td>{{ $vente->client->identiteCl ?? 'Non renseigné' }}</td>
                                <td>{{ $vente->vendeur->nomU ?? 'Non renseigné' }}</td>
                                <td>
                                    <ul>
                                        @foreach ($vente->lignesVente as $ligne)
                                            <li>
                                                {{ $ligne->produit->NomP ?? 'Produit inconnu' }} 
                                                ({{ $ligne->qteLVente }} x {{ number_format($ligne->prixLVente, 2, ',', ' ') }} FCFA)
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    
                </table>
            </div>

            @if ($ventes->isEmpty())
                <div class="alert alert-warning text-center">Aucune vente trouvée.</div>
            @endif

        </div>
    </div>
</div>
@endsection
