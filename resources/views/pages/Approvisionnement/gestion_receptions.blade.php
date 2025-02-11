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
        <div class="card mb-5 me-3">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Liste des réceptions</h4>
                    </div><!--end col-->
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addReceptionModal">
                            <i class="fa-solid fa-plus me-1"></i> Ajouter une réception
                        </button>
                    </div><!--end col-->
                </div><!--end row-->
            </div><!--end card-header-->
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table mb-0" id="datatable_1">
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
                                        <input type="checkbox" class="form-check-input" name="check"
                                            id="customCheck{{ $reception->idReception }}">
                                    </td>
                                    <td class="text-center">{{ $reception->numReception }}</td>
                                    <td class="text-center">{{ $reception->dateReception }}</td>
                                    <td class="text-center">{{ $reception->RefNumBonReception }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">
                                            <!-- Bouton Modifier -->
                                            <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal"
                                                data-bs-target="#editReceptionModal{{ $reception->idReception }}">
                                                Modifier
                                            </button>
                                            <form method="POST" action="{{ route('receptions.destroy', $reception->idReception) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"
                                                    onclick="return confirm('Voulez-vous vraiment supprimer cette réception ?')">
                                                    Supprimer
                                                </button>
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
                </div><!-- end table-responsive -->
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div><!-- end row -->

    <!-- Modal Ajouter une réception -->
    <div class="modal fade" id="addReceptionModal" tabindex="-1" aria-labelledby="addReceptionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addReceptionModalLabel">Ajouter une réception</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div><!-- end modal-header -->
                <div class="modal-body">
                    <form method="POST" action="{{ route('receptions.store') }}" id="formCreation">
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
                                <label for="RefNumBonReception" class="form-label">Référence du bordereau</label>
                                <input type="text" name="RefNumBonReception" id="RefNumBonReception" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label">Produits</label>
                                    <button type="button" class="btn btn-secondary" id="ajouterLigneBtn">
                                        Ajouter une ligne
                                    </button>
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
                                                <select name="lignes[0][idP]" class="form-select product-select" required>
                                                    <option value="">Sélectionner un produit</option>
                                                    @foreach ($produits as $produit)
                                                        <option value="{{ $produit->idP }}"
                                                            data-qteRestante="{{ $produit->qteRestante }}"
                                                            data-prix="{{ $produit->ligneCommandes->first()->prix ?? 0 }}">
                                                            {{ $produit->NomP }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select name="lignes[0][idMagasin]" class="form-select" required>
                                                    <option value="">Sélectionner un magasin</option>
                                                    @foreach ($magasins as $magasin)
                                                        <option value="{{ $magasin->idMgs }}">
                                                            {{ $magasin->libelleMgs }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="lignes[0][qteLivre]" class="form-control qteLivr" required>
                                            </td>
                                            <td>
                                                <input type="number" name="lignes[0][qteRestant]" class="form-control qteRestant" readonly>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="lignes[0][prixUn]" class="form-control" required>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger supprimer-ligne">Supprimer</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div><!-- end col -->
                        </div><!-- end row -->

                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div><!-- end modal-body -->
            </div><!-- end modal-content -->
        </div><!-- end modal-dialog -->
    </div><!-- end modal -->

    <!-- Modal Modifier une réception -->
    @foreach ($receptions as $reception)
    <div class="modal fade" id="editReceptionModal{{ $reception->idReception }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier la réception</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div><!-- end modal-header -->
                <div class="modal-body">
                    <form method="POST" action="{{ route('receptions.update', $reception->idReception) }}" class="formEdition" data-id="{{ $reception->idReception }}">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Numéro de réception</label>
                                <input type="text" name="numReception" class="form-control" value="{{ $reception->numReception }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date de réception</label>
                                <input type="date" name="dateReception" class="form-control" value="{{ \Carbon\Carbon::parse($reception->dateReception)->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Référence du bordereau</label>
                            <input type="text" name="RefNumBonReception" class="form-control" value="{{ $reception->RefNumBonReception }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Produits</label>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Magasin</th>
                                        <th>Quantité livrée</th>
                                        <th>Quantité restante</th>
                                        <th>Prix unitaire</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="ligneProduitsEdit" id="ligneProduitsEdit{{ $reception->idReception }}">
                                    @foreach ($reception->lignesReceptions as $index => $ligne)
                                        <tr>
                                            <td>
                                                <select name="lignes[{{ $index }}][idP]" class="form-select product-select" required>
                                                    @foreach ($produits as $produit)
                                                        <option value="{{ $produit->idP }}"
                                                            data-qteRestante="{{ $produit->qteRestante }}"
                                                            data-prix="{{ optional($produit->ligneCommandes->first())->prix ?? 0 }}"
                                                            {{ $produit->idP == $ligne->idP ? 'selected' : '' }}>
                                                            {{ $produit->NomP }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select name="lignes[{{ $index }}][idMagasin]" class="form-select" required>
                                                    @foreach ($magasins as $magasin)
                                                        <option value="{{ $magasin->idMgs }}"
                                                            {{ $magasin->idMgs == $ligne->idMagasin ? 'selected' : '' }}>
                                                            {{ $magasin->libelleMgs }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="lignes[{{ $index }}][qteLivre]" class="form-control qteLivr" value="{{ $ligne->qteReception }}" required>
                                            </td>
                                            <td>
                                                <input type="number" name="lignes[{{ $index }}][qteRestant]" class="form-control qteRestant" value="{{ $ligne->qteRestante }}" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="lignes[{{ $index }}][prixUn]" class="form-control" value="{{ $ligne->prixUn }}" required>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger supprimer-ligne">Supprimer</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary btn-ajouter-ligne-edit" data-reception="{{ $reception->idReception }}">
                                    Ajouter une ligne
                                </button>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                        </div>
                    </form>
                </div><!-- end modal-body -->
            </div><!-- end modal-content -->
        </div><!-- end modal-dialog -->
    </div><!-- end modal -->
@endforeach


</div><!-- end container -->

{{-- Script JavaScript --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let ligneIndexCreation = 1; // Index pour le formulaire de création
    
        function generateLigneHTML(index, isEdition = false) {
            let produitsOptions = `<option value="">Sélectionner un produit</option>
                @foreach ($produits as $produit)
                    <option value="{{ $produit->idP }}"
                        data-qteRestante="{{ $produit->qteRestante }}"
                        data-prix="{{ $produit->ligneCommandes->first()->prix ?? 0 }}">
                        {{ $produit->NomP }}
                    </option>
                @endforeach`;
            let magasinsOptions = `<option value="">Sélectionner un magasin</option>
                @foreach ($magasins as $magasin)
                    <option value="{{ $magasin->idMgs }}">{{ $magasin->libelleMgs }}</option>
                @endforeach`;
            return `<tr>
                <td>
                    <select name="lignes[${index}][idP]" class="form-select product-select" required>
                        ${produitsOptions}
                    </select>
                </td>
                <td>
                    <select name="lignes[${index}][idMagasin]" class="form-select" required>
                        ${magasinsOptions}
                    </select>
                </td>
                <td>
                    <input type="number" name="lignes[${index}][qteLivre]" class="form-control qteLivr" required>
                </td>
                <td>
                    <input type="number" name="lignes[${index}][qteRestant]" class="form-control qteRestant" readonly>
                </td>
                <td>
                    <input type="number" step="0.01" name="lignes[${index}][prixUn]" class="form-control" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger supprimer-ligne">Supprimer</button>
                </td>
            </tr>`;
        }
    
        document.getElementById('ajouterLigneBtn').addEventListener('click', function () {
            const tbody = document.getElementById('ligneProduits');
            tbody.insertAdjacentHTML('beforeend', generateLigneHTML(ligneIndexCreation));
            ligneIndexCreation++;
        });
    
        document.querySelectorAll('.btn-ajouter-ligne-edit').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const idReception = btn.getAttribute('data-reception');
                const tbody = document.getElementById('ligneProduitsEdit' + idReception);
                const index = tbody.querySelectorAll('tr').length;
                tbody.insertAdjacentHTML('beforeend', generateLigneHTML(index, true));
            });
        });
    
        function reindexLignes(tbody) {
            tbody.querySelectorAll('tr').forEach((row, index) => {
                row.querySelectorAll('input, select').forEach(input => {
                    input.name = input.name.replace(/lignes\[\d+\]/, `lignes[${index}]`);
                });
            });
        }
    
        document.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('supprimer-ligne')) {
                const row = e.target.closest('tr');
                const tbody = row.parentElement;
                row.remove();
                reindexLignes(tbody);
            }
        });
    
        function updateRow(selectElement) {
            const row = selectElement.closest('tr');
            const selectedOption = selectElement.selectedOptions[0];
            const qteRestante = parseFloat(selectedOption.getAttribute('data-qteRestante')) || 0;
            const prix = parseFloat(selectedOption.getAttribute('data-prix')) || 0;
            const qteRestInput = row.querySelector('input[name$="[qteRestant]"]');
            const prixInput = row.querySelector('input[name$="[prixUn]"]');
            if (qteRestInput) qteRestInput.value = qteRestante;
            if (prixInput) prixInput.value = prix;
        }
    
        document.addEventListener('change', function (e) {
            if (e.target && e.target.classList.contains('product-select')) {
                updateRow(e.target);
            }
        });
    
        document.addEventListener('input', function (e) {
            if (e.target && e.target.classList.contains('qteLivr')) {
                const inputQteLivr = e.target;
                const row = inputQteLivr.closest('tr');
                const productSelect = row.querySelector('.product-select');
                if (!productSelect || productSelect.selectedIndex === -1) return;
                const selectedOption = productSelect.selectedOptions[0];
                const initialRestante = parseFloat(selectedOption.getAttribute('data-qteRestante')) || 0;
                let qteLivre = parseFloat(inputQteLivr.value) || 0;
                if (qteLivre > initialRestante) {
                    qteLivre = initialRestante;
                    inputQteLivr.value = initialRestante;
                }
                const newRestante = initialRestante - qteLivre;
                const qteRestInput = row.querySelector('input[name$="[qteRestant]"]');
                if (qteRestInput) {
                    qteRestInput.value = newRestante;
                }
            }
        });
    
        // Mise à jour automatique dans les formulaires d'édition dès le chargement
        document.querySelectorAll('.formEdition select.product-select').forEach(function(select) {
            updateRow(select);
        });
    
        // OU : Mise à jour lors de l'ouverture d'un modal d'édition (optionnel)
        document.querySelectorAll('.modal').forEach(function(modal) {
            modal.addEventListener('shown.bs.modal', function() {
                modal.querySelectorAll('select.product-select').forEach(function(select) {
                    updateRow(select);
                });
            });
        });
    });
    </script>
    
@endsection
