@extends('layouts.master')
@section('content')
    <div class="container-xxl">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Liste des commandes d'achat</h4>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="fa-solid fa-plus me-1"></i> Nouvelle commande
                                </button>
                            </div>
                        </div>
                    </div>

                    @if (Session::has('status'))
                        <div class="alert alert-success alert-dismissible">
                            {{ Session::get('status') }}
                        </div>
                    @endif

                    @if (Session::has('erreur'))
                        <div class="alert alert-danger alert-dismissible">
                            {{ Session::get('erreur') }}
                        </div>
                    @endif

                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table mb-0" id="datatable_1">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">Réf Cmd</th>
                                        <th class="text-center">Fournisseur</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Délais</th>
                                        <th class="text-center">Montant HT</th>
                                        <th class="text-center">Montant TTC</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($commandes as $cmd)
                                        <tr>
                                            <td class="text-center">{{ $cmd->reference }}</td>
                                            <td class="text-center">{{ $cmd->fournisseur->nom }}</td>
                                            <td class="text-center">{{ $cmd->dateOp }}</td>
                                            <td class="text-center">{{ $cmd->delailivraison }}</td>
                                            <td class="text-center">{{ $cmd->montantTotalHT }}</td>
                                            <td class="text-center">{{ $cmd->montantTotalTTC }}</td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-primary me-1" data-bs-toggle="modal"
                                                    data-bs-target="#editModal{{ $cmd->idCommande }}">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $cmd->idCommande }}">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    {{-- Ajout --}}
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Nouvelle commande d'achat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('commandeAchat.store') }}">
                    @csrf
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Fournisseur</label>
                                <select name="idF" class="form-select" required>
                                    <option value="">-- Choisir --</option>
                                    @foreach ($fournisseurs as $fr)
                                        <option value="{{ $fr->idF }}">{{ $fr->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Référence</label>
                                <input type="text" name="reference" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Date</label>
                                <input type="datetime-local" name="dateOp" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Délais livraison</label>
                                <input type="text" name="delailivraison" class="form-control" required>
                            </div>
                        </div>
                        <table class="table table-bordered mb-3">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Montant HT</th>
                                    <th>tva (%)</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="tableAddLines"></tbody>
                        </table>
                        <button type="button" class="btn btn-secondary" onclick="addLine('tableAddLines')">
                            Ajouter une ligne
                        </button>
                        <input type="hidden" name="montantTotalHT" value="0">
                        <input type="hidden" name="montantTotalTTC" value="0">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modification --}}
    @foreach ($commandes as $cmd)
        <div class="modal fade" id="editModal{{ $cmd->idCommande }}" tabindex="-1"
            aria-labelledby="editModalLabel{{ $cmd->idCommande }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel{{ $cmd->idCommande }}">Modifier commande
                            {{ $cmd->reference }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('commandeAchat.update', $cmd->idCommande) }}">
                        @csrf @method('PUT')
                        <div class="modal-body">
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Fournisseur</label>
                                    <select name="idF" class="form-select">
                                        @foreach ($fournisseurs as $fr)
                                            <option value="{{ $fr->idF }}"
                                                {{ $cmd->idF == $fr->idF ? 'selected' : '' }}>{{ $fr->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Référence</label>
                                    <input type="text" name="reference" class="form-control"
                                        value="{{ $cmd->reference }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Date</label>
                                    <input type="datetime-local" name="dateOp" class="form-control"
                                        value="{{ \Carbon\Carbon::parse($cmd->dateOp)->format('Y-m-d\TH:i') }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Délais livraison</label>
                                    <input type="text" name="delailivraison" class="form-control"
                                        value="{{ $cmd->delailivraison }}">
                                </div>
                            </div>
                            <table class="table table-bordered mb-3">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Quantité</th>
                                        <th>Montant HT</th>
                                        <th>tva (%)</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="tableEditLines{{ $cmd->idCommande }}">
                                    @foreach ($cmd->lignes as $index => $l)
                                        <tr>
                                            <td>
                                                <select name="lignes[{{ $index }}][idPro]" class="form-select"
                                                    onchange="gettva(this, {{ $index }})">
                                                    @foreach ($produits as $prd)
                                                        <option value="{{ $prd->idPro }}"
                                                            data-tva="{{ $prd->familleProduit->TVA ?? 0 }}">
                                                            {{ $prd->libelle }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="lignes[{{ $index }}][idDetailCom]"
                                                    value="{{ $l->idDetailCom }}">
                                            </td>
                                            <td><input type="number" name="lignes[{{ $index }}][qteCmd]"
                                                    class="form-control" value="{{ $l->qteCmd }}"></td>
                                            <td><input type="number" name="lignes[{{ $index }}][montantHT]"
                                                    class="form-control" value="{{ $l->montantHT }}"></td>
                                            <td><input type="number" name="lignes[{{ $index }}][tva]"
                                                    class="form-control" value="{{ $l->tva }}" readonly></td>
                                            <td><button type="button" class="btn btn-danger"
                                                    onclick="this.closest('tr').remove()">Supprimer</button></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-secondary"
                                onclick="addLine('tableEditLines{{ $cmd->idCommande }}')">Ajouter une ligne</button>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Modal de confirmation de suppression --}}
    @foreach ($commandes as $cmd)
        <div class="modal fade" id="deleteModal{{ $cmd->idCommande }}" tabindex="-1"
            aria-labelledby="deleteModalLabel{{ $cmd->idCommande }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel{{ $cmd->idCommande }}">Confirmation de suppression
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer la commande <strong>{{ $cmd->reference }}</strong> ?</p>
                        <p class="text-danger">Cette action est irréversible.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <form action="{{ route('commandeAchat.destroy', $cmd->idCommande) }}" method="POST"
                            style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        let nextIndex = 0;

        // Fonction pour charger la tva au chargement de la page
        function loadInitialtva() {
            document.querySelectorAll('select[name^="lignes"][name$="[idPro]"]').forEach((select, index) => {
                const selectedOption = select.options[select.selectedIndex];
                if (selectedOption) {
                    const tva = selectedOption.getAttribute('data-tva');
                    const tvaInput = select.closest('tr').querySelector(`input[name="lignes[${index}][tva]"]`);
                    if (tvaInput) {
                        tvaInput.value = tva;
                    }
                }
            });
        }

        // Fonction pour calculer le montant TTC d'une ligne
        function calculerMontantTTC(row) {
            const qte = parseFloat(row.querySelector('input[name$="[qteCmd]"]').value) || 0;
            const montantHT = parseFloat(row.querySelector('input[name$="[montantHT]"]').value) || 0;
            const tva = parseFloat(row.querySelector('input[name$="[tva]"]').value) || 0;

            const montantTTC = montantHT * (1 + tva / 100);
            return montantTTC;
        }

        // Fonction pour mettre à jour les totaux
        function updateTotaux() {
            let totalHT = 0;
            let totalTTC = 0;

            document.querySelectorAll('tbody[id^="table"] tr').forEach(row => {
                const montantHT = parseFloat(row.querySelector('input[name$="[montantHT]"]').value) || 0;
                const montantTTC = calculerMontantTTC(row);

                totalHT += montantHT;
                totalTTC += montantTTC;
            });

            // Mettre à jour les champs de totaux si ils existent
            const totalHTInput = document.querySelector('input[name="montantTotalHT"]');
            const totalTTCInput = document.querySelector('input[name="montantTotalTTC"]');

            if (totalHTInput) totalHTInput.value = totalHT.toFixed(2);
            if (totalTTCInput) totalTTCInput.value = totalTTC.toFixed(2);
        }

        function addLine(tableId) {
            const tbody = document.getElementById(tableId);
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <select name="lignes[${nextIndex}][idPro]" class="form-select" onchange="gettva(this, ${nextIndex})" required>
                        <option value="">-- Produit --</option>
                        @foreach ($produits as $prd)
                            <option value="{{ $prd->idPro }}" data-tva="{{ $prd->familleProduit->TVA ?? 0 }}">
                                {{ $prd->libelle }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="lignes[${nextIndex}][qteCmd]" class="form-control" required min="1" onchange="updateTotaux()"></td>
                <td><input type="number" name="lignes[${nextIndex}][montantHT]" class="form-control" required min="0" step="0.01" onchange="updateTotaux()"></td>
                <td><input type="number" name="lignes[${nextIndex}][tva]" class="form-control" readonly></td>
                <td><button type="button" class="btn btn-danger" onclick="removeLine(this)">Supprimer</button></td>
            `;
            tbody.appendChild(row);
            nextIndex++;
        }

        function removeLine(button) {
            const row = button.closest('tr');
            row.remove();
            updateTotaux();
        }

        function gettva(selectElement, index) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const tvaInput = selectElement.closest('tr').querySelector(`input[name="lignes[${index}][tva]"]`);

            if (selectedOption && selectedOption.value) {
                const tva = selectedOption.getAttribute('data-tva');
                if (tvaInput) {
                    tvaInput.value = tva;
                    updateTotaux(); // Mettre à jour les totaux quand la TVA change
                }
            } else {
                if (tvaInput) {
                    tvaInput.value = '0';
                    updateTotaux();
                }
            }
        }

        // Fonction pour réinitialiser les erreurs dans le modal
        function resetModalErrors(modal) {
            // Réinitialiser le contenu des messages d'erreur
            var errorElements = modal.querySelectorAll('.invalid-feedback');
            errorElements.forEach(function(errorElement) {
                errorElement.textContent = ''; // Supprimer le texte des erreurs
            });

            // Réinitialiser les champs de saisie
            var inputFields = modal.querySelectorAll('.form-control');
            inputFields.forEach(function(inputField) {
                inputField.classList.remove('is-invalid'); // Enlever la classe d'erreur
            });

            // Effacer les erreurs dans la session si besoin
            @if (Session::has('error'))
                @php
                    session()->forget('error');
                @endphp
            @endif
        }

        // Ajouter les écouteurs d'événements pour les modals
        document.addEventListener('DOMContentLoaded', function() {
            var modals = document.querySelectorAll('.modal');

            modals.forEach(function(modal) {
                modal.addEventListener('hidden.bs.modal', function() {
                    resetModalErrors(modal);
                });

                modal.addEventListener('shown.bs.modal', function() {
                    loadInitialtva();
                    updateTotaux();
                });

                var cancelButton = modal.querySelector('.btn-secondary');
                if (cancelButton) {
                    cancelButton.addEventListener('click', function() {
                        resetModalErrors(modal);
                    });
                }
            });

            // Initialiser les totaux au chargement de la page
            updateTotaux();
        });
    </script>
@endsection
