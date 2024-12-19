@extends('layouts.master')

@section('content')
    <div class="container">
        <h1 class="mt-4">Gestion des Réceptions</h1>

        <!-- Message de succès -->
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Formulaire d'ajout d'une réception -->
        <div class="card mb-4">
            <div class="card-header">Ajouter une réception</div>
            <div class="card-body">
                <form method="POST" action="{{ route('receptions.store') }}">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="numReception" class="form-label">Numéro de réception</label>
                            <input type="text" name="numReception" id="numReception" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="dateReception" class="form-label">Date de réception</label>
                            <input type="date" name="dateReception" id="dateReception" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="RefNumBordReception" class="form-label">Référence du bordereau</label>
                            <input type="text" name="RefNumBordReception" id="RefNumBordReception" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Produits</label>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Magasin</th>
                                        <th>Quantité livrée</th>
                                        <th>Quantité restante à livrer</th>
                                        <th>Prix unitaire</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="ligneProduits">
                                    <tr>
                                        <td>
                                            <select name="lignes[0][idP]" class="form-select" required>
                                                <option value="">Sélectionner un produit</option>
                                                @foreach ($produits as $produit)
                                                    <option value="{{ $produit->idP }}">{{ $produit->NomP }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="lignes[0][idMagasin]" class="form-select" required>
                                                <option value="">Sélectionner un magasin</option>
                                                @foreach ($magasins as $magasin)
                                                    <option value="{{ $magasin->idMgs }}">{{ $magasin->libelleMgs }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        
                                        <td>
                                            <input type="number" name="lignes[0][qteLivr]" class="form-control qteLivr" required>
                                        </td>
                                        <td>
                                            <input type="number" name="lignes[0][qteRestant]" class="form-control qteRestant" 
                                                value="{{ $ligneCommandes->firstWhere('idP', $produit->idP)->qteRest ?? '' }}" readonly>
                                        </td>
                                        
                                        <td>
                                            <input type="number" step="0.01" name="lignes[0][prixUn]"
                                                class="form-control"
                                                value="{{ $ligneCommandes->firstWhere('idP', $produit->idP)->prix ?? '' }}"
                                                readonly>
                                        </td>
                                        <td><button type="button" class="btn btn-danger"
                                                onclick="supprimerLigne(this)">Supprimer</button></td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-secondary" onclick="ajouterLigne()">Ajouter une
                                ligne</button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </form>
            </div>
        </div>

        <!-- Liste des réceptions -->
        <div class="card">
            <div class="card-header">Liste des réceptions</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Numéro</th>
                            <th>Date</th>
                            <th>Référence bordereau</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($receptions as $reception)
                            <tr>
                                <td>{{ $reception->numReception }}</td>
                                <td>{{ $reception->dateReception }}</td>
                                <td>{{ $reception->RefNumBordReception }}</td>
                                <td>
                                    <form method="POST"
                                        action="{{ route('receptions.destroy', $reception->idReception) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">Aucune réception enregistrée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        let ligneIndex = 1;

        function ajouterLigne() {
            const ligne = `<tr>
            <td>
                <select name="lignes[${ligneIndex}][idP]" class="form-select" required>
                    <option value="">Sélectionner un produit</option>
                    @foreach ($produits as $produit)
                        <option value="{{ $produit->idP }}">{{ $produit->NomP }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="lignes[${ligneIndex}][idMagasin]" class="form-select" required>
                    <option value="">Sélectionner un magasin</option>
                    @foreach ($magasins as $magasin)
                        <option value="{{ $magasin->idMagasin }}">{{ $magasin->NomMagasin }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="lignes[0][qteLivr]" class="form-control qteLivr" required>
            </td>
            <td>
                <input type="number" name="lignes[0][qteRestant]" class="form-control qteRestant" 
                value="{{ $ligneCommandes->firstWhere('idP', $produit->idP)->qteRest ?? '' }}" readonly>
            </td>
            <td>
                <input type="number" step="0.01" name="lignes[0][prixUn]"
                class="form-control"
                value="{{ $ligneCommandes->firstWhere('idP', $produit->idP)->prix ?? '' }}"
                readonly>
            </td>
            <td><button type="button" class="btn btn-danger" onclick="supprimerLigne(this)">Supprimer</button></td>
            </tr>`;
            document.getElementById('ligneProduits').insertAdjacentHTML('beforeend', ligne);
            ligneIndex++;
        }

        function supprimerLigne(button) {
            button.closest('tr').remove();
        }

        document.addEventListener('input', function (event) {
    if (event.target.name && event.target.name.includes('[qteLivr]')) {
        const row = event.target.closest('tr');
        const qteLivr = parseFloat(event.target.value) || 0;
        const qteRestantField = row.querySelector('input[name$="[qteRestant]"]');
        const qteRestant = parseFloat(qteRestantField.value) || 0;

        if (qteLivr > qteRestant) {
            alert('La quantité livrée ne peut pas dépasser la quantité restante à livrer.');
            event.target.value = qteRestant; // Réinitialiser à la valeur maximale autorisée
        }
    }
});

    </script>
@endsection
