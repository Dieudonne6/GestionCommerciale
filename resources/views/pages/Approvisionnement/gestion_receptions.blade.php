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
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addReceptionModal">
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
                                            <input type="checkbox" class="form-check-input" name="select-all"
                                                id="select-all">
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
                                                <form method="POST"
                                                    action="{{ route('receptions.destroy', $reception->idReception) }}">
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
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
                                    <label for="RefNumBonReception" class="form-label">Référence du bordereau</label>
                                    <input type="text" name="RefNumBonReception" id="RefNumBonReception" class="form-control" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label">Produits</label>
                                        <button type="button" class="btn btn-secondary my-2 mx-3"
                                            onclick="ajouterLigne()">Ajouter une ligne</button>
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
                                                    <select name="lignes[0][idP]" class="form-select" required onchange="updateRow(this)">
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
                                                    <button type="button" class="btn btn-danger" onclick="supprimerLigne(this)">Supprimer</button>
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
        
        <!-- Modal Modifier une réception -->
        @foreach ($receptions as $reception)
            <div class="modal fade" id="editReceptionModal{{ $reception->idReception }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Modifier la réception</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('receptions.update', $reception->idReception) }}">
                                @csrf 
                                @method('PUT')
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Numéro de réception</label>
                                        <input type="text" name="numReception" class="form-control" value="{{ $reception->numReception }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Date de réception</label>
                                        <input  name="dateReception" class="form-control" value="{{ $reception->dateReception }}" required>
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
                                        <tbody id="ligneProduitsEdit{{ $reception->idReception }}">
                                            @foreach ($reception->lignesReceptions as $index => $ligne)
                                                <tr>
                                                    <td>
                                                        <select name="lignes[{{ $index }}][idP]" class="form-select" required onchange="updateRow(this)">
                                                            @foreach ($produits as $produit)
                                                                <option value="{{ $produit->idP }}" 
                                                                    data-qteRestante="{{ $produit->qteRestante }}"
                                                                    data-prix="{{ $produit->ligneCommandes->first()->prix ?? 0 }}"
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
                                                        <input type="number" name="lignes[{{ $index }}][qteRestant]" class="form-control qteRestant" value="{{ $ligne->qteRestante ?? '' }}" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" name="lignes[{{ $index }}][prixUn]" class="form-control" value="{{ $ligne->prixUn }}" required>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger" onclick="supprimerLigne(this)">Supprimer</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-secondary" onclick="ajouterLigneEdit({{ $reception->idReception }})">
                                            Ajouter une ligne
                                        </button>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>

    <script>
        let ligneIndex = 1; // Départ à 1 car une ligne est déjà présente dans le formulaire d'ajout
    
        // Ajoute une nouvelle ligne dans le formulaire de création
        function ajouterLigne() {
            const ligneProduits = document.getElementById('ligneProduits');
            if (!ligneProduits) {
                alert('Erreur : le conteneur des lignes de produits est introuvable.');
                return;
            }
            const ligne = document.createElement('tr');
            ligne.innerHTML = `
                <td>
                    <select name="lignes[${ligneIndex}][idP]" class="form-select" required onchange="updateRow(this)">
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
                    <select name="lignes[${ligneIndex}][idMagasin]" class="form-select" required>
                        <option value="">Sélectionner un magasin</option>
                        @foreach ($magasins as $magasin)
                            <option value="{{ $magasin->idMgs }}">{{ $magasin->libelleMgs }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="lignes[${ligneIndex}][qteLivre]" class="form-control qteLivr" required>
                </td>
                <td>
                    <input type="number" name="lignes[${ligneIndex}][qteRestant]" class="form-control qteRestant" readonly>
                </td>
                <td>
                    <input type="number" step="0.01" name="lignes[${ligneIndex}][prixUn]" class="form-control" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger" onclick="supprimerLigne(this)">Supprimer</button>
                </td>
            `;
            ligneProduits.appendChild(ligne);
            ligneIndex++;
        }
    
        // Ajoute une ligne dans le formulaire d'édition d'une réception donnée
        function ajouterLigneEdit(idReception) {
            const tbody = document.getElementById('ligneProduitsEdit' + idReception);
            if (!tbody) {
                alert('Erreur : le conteneur des lignes de produits est introuvable.');
                return;
            }
            const ligne = document.createElement('tr');
            // On récupère le nombre actuel de lignes pour ce formulaire
            let index = tbody.querySelectorAll('tr').length;
            ligne.innerHTML = `
                <td>
                    <select name="lignes[${index}][idP]" class="form-select" required onchange="updateRow(this)">
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
                    <select name="lignes[${index}][idMagasin]" class="form-select" required>
                        <option value="">Sélectionner un magasin</option>
                        @foreach ($magasins as $magasin)
                            <option value="{{ $magasin->idMgs }}">{{ $magasin->libelleMgs }}</option>
                        @endforeach
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
                    <button type="button" class="btn btn-danger" onclick="supprimerLigne(this)">Supprimer</button>
                </td>
            `;
            tbody.appendChild(ligne);
        }
    
        // Supprime la ligne contenant le bouton déclencheur et réindexe si nécessaire
        function supprimerLigne(button) {
            button.closest('tr').remove();
            updateRowIndices();
        }
    
        // Met à jour les informations de la ligne (quantité restante et prix unitaire) en fonction du produit sélectionné
        function updateRow(selectElement) {
            const row = selectElement.closest('tr');
            const selectedOption = selectElement.selectedOptions[0];
            const qteRestante = parseFloat(selectedOption.getAttribute('data-qteRestante')) || 0;
            const prix = parseFloat(selectedOption.getAttribute('data-prix')) || 0;
    
            // Met à jour les inputs correspondants dans la même ligne
            const qteRestInput = row.querySelector('input[name$="[qteRestant]"]');
            const prixInput = row.querySelector('input[name$="[prixUn]"]');
    
            if(qteRestInput) {
                // Au départ, la quantité restante est la valeur initiale
                qteRestInput.value = qteRestante;
            }
            if(prixInput) {
                prixInput.value = prix;
            }
        }
    
        // Réindexe les noms des inputs dans le formulaire de création après une suppression
        function updateRowIndices() {
            const rows = document.querySelectorAll('#ligneProduits tr');
            rows.forEach((row, index) => {
                row.querySelectorAll('input, select').forEach(input => {
                    input.name = input.name.replace(/lignes\[\d+\]/, `lignes[${index}]`);
                });
            });
            ligneIndex = rows.length;
        }
    
        // Écouteur d'événement sur les inputs de quantité livrée pour mettre à jour la quantité restante
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('qteLivr')) {
                const row = e.target.closest('tr');
    
                // Récupère le select du produit dans la même ligne
                const productSelect = row.querySelector('select[name$="[idP]"]');
                if (!productSelect || productSelect.selectedIndex === -1) return;
    
                const selectedOption = productSelect.selectedOptions[0];
                // La quantité initiale disponible du produit
                const initialRestante = parseFloat(selectedOption.getAttribute('data-qteRestante')) || 0;
                // La quantité livrée saisie par l'utilisateur
                const qteLivre = parseFloat(e.target.value) || 0;
                // Calcul de la quantité restante
                const newRestante = initialRestante - qteLivre;
    
                // Met à jour l'input correspondant
                const qteRestInput = row.querySelector('input[name$="[qteRestant]"]');
                if(qteRestInput) {
                    qteRestInput.value = newRestante;
                }
            }
        });
    </script>

@endsection
