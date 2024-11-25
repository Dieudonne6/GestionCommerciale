@extends('layouts.master')

@section('content')

<div class="container">
    <h1 class="mb-4">Gestion des Caisses</h1>

    <!-- Résumé des caisses -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Solde Total</h5>
                    <p class="text-success fw-bold">15,000 €</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Total Entrées</h5>
                    <p class="text-primary fw-bold">20,000 €</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Total Sorties</h5>
                    <p class="text-danger fw-bold">5,000 €</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire d'ajout -->
    <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addTransactionModal">Ajouter une Transaction</button>

    <!-- Tableau des transactions -->
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Référence</th>
                    <th>Type</th>
                    <th>Montant</th>
                    <th>Mode de Paiement</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>2024-11-24</td>
                    <td>REC-001</td>
                    <td>Entrée</td>
                    <td class="text-success">+500 €</td>
                    <td>Espèces</td>
                    <td>Vente en magasin</td>
                    <td>
                        <button class="btn btn-sm btn-warning">Modifier</button>
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                        <button class="btn btn-sm btn-info">Imprimer</button>
                    </td>
                </tr>
                <!-- Autres transactions -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal d'ajout de transaction -->
<div class="modal fade" id="addTransactionModal" tabindex="-1" aria-labelledby="addTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        {{-- <form method="POST" action="{{ route('transactions.store') }}"> --}}
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTransactionModalLabel">Ajouter une Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="caisse" class="form-label">Caisse</label>
                        <select id="caisse" name="caisse" class="form-select">
                            <option value="1">Caisse Principale</option>
                            <option value="2">Caisse Secondaire</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Type de Transaction</label>
                        <select id="type" name="type" class="form-select">
                            <option value="entrée">Entrée</option>
                            <option value="sortie">Sortie</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="montant" class="form-label">Montant</label>
                        <input type="number" id="montant" name="montant" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="mode" class="form-label">Mode de Paiement</label>
                        <select id="mode" name="mode" class="form-select">
                            <option value="espèces">Espèces</option>
                            <option value="carte">Carte Bancaire</option>
                            <option value="virement">Virement</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                </div>
            </div>
        {{-- </form> --}}
    </div>
</div>

@endsection