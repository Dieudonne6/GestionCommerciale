@extends('layouts.master')

@section('content')
    <div class="container">
        <h1 class="mt-4">Gestion des Réceptions</h1>

        <!-- Message de succès -->
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Liste des réceptions -->
        <div class="row">
            <div class="card" style="margin-bottom: 150px; margin-right: 50px;">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Liste des réceptions</h4>
                        </div><!--end col-->
                        <div class="col-auto">
                            <form class="row g-2">
                                <div class="col-auto">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReceptionModal">
                                        <i class="fa-solid fa-plus me-1"></i> Ajouter une réception
                                    </button>
                                </div><!--end col-->
                            </form>
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!--end card-header-->
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table mb-0 checkbox-all" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 16px;">
                                        <div class="form-check mb-0 ms-n1">
                                            <input type="checkbox" class="form-check-input" name="select-all" id="select-all">
                                        </div>
                                    </th>
                                    <th class="ps-0 text-center">Numéro</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Référence bordereau</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($receptions as $reception)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input" name="check" id="customCheck1">
                                        </td>
                                        <td class="text-center">{{ $reception->numReception }}</td>
                                        <td class="text-center">{{ $reception->dateReception }}</td>
                                        <td class="text-center">{{ $reception->RefNumBordReception }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <form method="POST" action="{{ route('receptions.destroy', $reception->idReception) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Aucune réception enregistrée.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        

        <!-- Modal Ajouter une réception -->
        <div class="modal fade" id="addReceptionModal" tabindex="-1" aria-labelledby="addReceptionModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addReceptionModalLabel">Ajouter une réception</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('receptions.store') }}">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="numReception" class="form-label">Numéro de réception</label>
                                    <input type="text" name="numReception" id="numReception" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label for="dateReception" class="form-label">Date de réception</label>
                                    <input type="date" name="dateReception" id="dateReception" class="form-control"
                                        required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="RefNumBordReception" class="form-label">Référence du bordereau</label>
                                    <input type="text" name="RefNumBordReception" id="RefNumBordReception"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label">Produits</label>
                                        <button type="button" class="btn btn-secondary my-2 mx-3" onclick="ajouterLigne()">Ajouter une ligne</button>
                                    </div>                                    
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
                                                            <option value="{{ $produit->idP }}">{{ $produit->NomP }}
                                                            </option>
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
                                                    <input type="number" name="lignes[0][qteLivr]"
                                                        class="form-control qteLivr" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="lignes[0][qteRestant]"
                                                        class="form-control qteRestant" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="lignes[0][prixUn]"
                                                        class="form-control" readonly>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger"
                                                        onclick="supprimerLigne(this)">Supprimer</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                            
                        </form>
                    </div>
                </div>
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
                        <option value="{{ $magasin->idMgs }}">{{ $magasin->libelleMgs }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="lignes[${ligneIndex}][qteLivr]" class="form-control qteLivr" required>
            </td>
            <td>
                <input type="number" name="lignes[${ligneIndex}][qteRestant]" class="form-control qteRestant" readonly>
            </td>
            <td>
                <input type="number" step="0.01" name="lignes[${ligneIndex}][prixUn]" class="form-control" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-danger" onclick="supprimerLigne(this)">Supprimer</button>
            </td>
            </tr>`;
            document.getElementById('ligneProduits').insertAdjacentHTML('beforeend', ligne);
            ligneIndex++;
        }

        function supprimerLigne(button) {
            button.closest('tr').remove();
        }

        document.addEventListener('input', function(event) {
            if (event.target.name && event.target.name.includes('[qteLivr]')) {
                const row = event.target.closest('tr');
                const qteLivr = parseFloat(event.target.value) || 0;
                const qteRestantField = row.querySelector('input[name$="[qteRestant]"]');
                const qteRestant = parseFloat(qteRestantField.value) || 0;

                if (qteLivr > qteRestant) {
                    alert
                        ('La quantité livrée ne peut pas dépasser la quantité restante à livrer.');
                    event.target.value = qteRestant; // Réinitialiser à la valeur maximale autorisée
                }
            }
        });
    </script>
@endsection
