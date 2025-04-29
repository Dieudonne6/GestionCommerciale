@extends('layouts.master')
@section('content')
    <div class="container-xxl">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <!-- En-tête -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Liste des réceptions</h4>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReceptionModal">
                            <i class="fa-solid fa-plus me-1"></i> Nouvelle réception
                        </button>
                    </div>

                    <!-- Flash messages -->
                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif
                    @if (session('erreur'))
                        <div class="alert alert-danger">{{ session('erreur') }}</div>
                    @endif

                    <!-- Tableau des réceptions -->
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table mb-0" id="datatable_receptions">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>Réf Cmd</th>
                                        <th>Date Réception</th>
                                        <th>Num. Bord.</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($receptions as $rec)
                                        <tr class="text-center">
                                            <td>{{ $rec->commandeAchat->reference }}</td>
                                            <td>{{ \Carbon\Carbon::parse($rec->date)->format('d/m/Y H:i') }}</td>
                                            <td>{{ $rec->numBordereauLivraison }}</td>
                                            <td>{{ ucfirst($rec->statutRecep) }}</td>
                                            <td>
                                                <!-- Modifier -->
                                                <button class="btn btn-sm btn-primary me-1" data-bs-toggle="modal"
                                                    data-bs-target="#editReceptionModal{{ $rec->idRecep }}">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <!-- Supprimer -->
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteReceptionModal{{ $rec->idRecep }}">
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

    {{-- ===== Modal: Nouvelle réception ===== --}}
    <div class="modal fade" id="addReceptionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('receptions.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nouvelle réception</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        {{-- erreurs --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $e)
                                        <li>{{ $e }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row g-3">
                            {{-- Choix de la commande --}}
                            <div class="col-md-6">
                                <label class="form-label">Commande</label>
                                <select name="idCommande" id="selectCommande" class="form-select">
                                    <option value="">-- choisir --</option>
                                    @foreach ($commandes as $cmd)
                                        <option value="{{ $cmd->idCommande }}">
                                            {{ $cmd->reference }} — {{ $cmd->fournisseur->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Date de réception --}}
                            <div class="col-md-6">
                                <label class="form-label">Date réception</label>
                                <input type="datetime-local" name="date" class="form-control"
                                    value="{{ now()->format('Y-m-d\TH:i') }}">
                            </div>
                            {{-- Référence --}}
                            <div class="col-md-6">
                                <label class="form-label">Référence</label>
                                <input type="text" name="reference" class="form-control" required>
                            </div>
                            {{-- Numéro de bordereau --}}
                            <div class="col-md-6">
                                <label class="form-label">N° bordereau livraison</label>
                                <input type="text" name="numBordereauLivraison" class="form-control">
                            </div>
                            {{-- Statut --}}
                            <div class="col-md-6">
                                <label class="form-label">Statut</label>
                                <select name="statutRecep" class="form-select">
                                    <option value="en_attente">En attente</option>
                                    <option value="partiel">Partiel</option>
                                    <option value="complet">Complet</option>
                                </select>
                            </div>
                        </div>

                        {{-- Tableau dynamique des lignes --}}
                        <table class="table table-bordered mt-4">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Qté réceptionnée</th>
                                    <th>Prix unitaire</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="receptionLines"></tbody>
                        </table>
                        <button type="button" class="btn btn-secondary" onclick="addReceptionLine()">
                            Ajouter une ligne
                        </button>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== JavaScript pour ajouter dynamiquement des lignes de réception ===== --}}
    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialisation de DataTable
                const table = $('#datatable_receptions').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json'
                    }
                });

                // Gestion du changement de commande
                $('#selectCommande').on('change', function() {
                    const idCommande = $(this).val();
                    if (!idCommande) {
                        $('#receptionLines').empty();
                        return;
                    }

                    $.get(`/receptions/commande/${idCommande}/details`, function(response) {
                        if (response.success) {
                            $('#receptionLines').empty();
                            response.details.forEach((detail, index) => {
                                addReceptionLine(detail, index);
                            });
                        }
                    });
                });
            });

            function addReceptionLine(detail = null, index = null) {
                const tbody = document.getElementById('receptionLines');
                const tr = document.createElement('tr');
                const recIndex = index !== null ? index : document.querySelectorAll('#receptionLines tr').length;

                tr.innerHTML = `
      <td>
        <select name="details[${recIndex}][idDetailCom]" class="form-select" required>
          <option value="">-- sélectionner --</option>
          @foreach ($commandes as $cmd)
            @foreach ($cmd->detailCommandeAchat as $d)
              <option value="{{ $d->idDetailCom }}"
                ${detail && detail.idDetailCom == {{ $d->idDetailCom }} ? 'selected' : ''}>
                {{ $d->produit->libelle ?? 'Produit #' . $d->idDetailCom }}
              </option>
            @endforeach
          @endforeach
        </select>
      </td>
      <td>
        <input type="number" 
               name="details[${recIndex}][qteReceptionne]" 
               class="form-control" 
               required 
               min="1"
               max="${detail ? detail.qteRestante : ''}"
               value="${detail ? detail.qteRestante : ''}">
      </td>
      <td>
        <input type="number" 
               step="0.01" 
               name="details[${recIndex}][prixUnit]" 
               class="form-control" 
               required 
               min="0"
               value="${detail ? detail.prixUnit : ''}">
      </td>
      <td>
        <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">
          &times;
        </button>
      </td>`;
                tbody.appendChild(tr);
            }
        </script>
    @endpush

    {{-- ================================
     Modals Édition des réceptions
================================ --}}
    @foreach ($receptions as $rec)
        <div class="modal fade" id="editReceptionModal{{ $rec->idRecep }}" tabindex="-1"
            aria-labelledby="editReceptionLabel{{ $rec->idRecep }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form method="POST" action="{{ route('receptions.update', $rec->idRecep) }}">
                    @csrf @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editReceptionLabel{{ $rec->idRecep }}">
                                Modifier réception – Cmd {{ $rec->commandeAchat->reference }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $e)
                                            <li>{{ $e }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Commande</label>
                                    <select name="idCommande" class="form-select">
                                        @foreach ($commandes as $cmd)
                                            <option value="{{ $cmd->idCommande }}"
                                                {{ $rec->idCommande == $cmd->idCommande ? 'selected' : '' }}>
                                                {{ $cmd->reference }} — {{ $cmd->fournisseur->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date réception</label>
                                    <input type="datetime-local" name="date" class="form-control"
                                        value="{{ \Carbon\Carbon::parse($rec->date)->format('Y-m-d\TH:i') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">N° bordereau livraison</label>
                                    <input type="text" name="numBordereauLivraison" class="form-control"
                                        value="{{ $rec->numBordereauLivraison }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Statut</label>
                                    <select name="statutRecep" class="form-select">
                                        <option value="en_attente"
                                            {{ $rec->statutRecep == 'en_attente' ? 'selected' : '' }}>
                                            En attente</option>
                                        <option value="partiel" {{ $rec->statutRecep == 'partiel' ? 'selected' : '' }}>
                                            Partiel</option>
                                        <option value="complet" {{ $rec->statutRecep == 'complet' ? 'selected' : '' }}>
                                            Complet</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Détails existants --}}
                            <table class="table table-bordered mt-4">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Qté réceptionnée</th>
                                        <th>Prix unitaire</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="editReceptionLines{{ $rec->idRecep }}">
                                    @foreach ($rec->detailReceptionCmdAchat as $i => $det)
                                        <tr>
                                            <td>
                                                <input type="hidden"
                                                    name="details[{{ $i }}][idDetailRecepCmdAchat]"
                                                    value="{{ $det->idDetailRecepCmdAchat }}">
                                                <select name="details[{{ $i }}][idDetailCom]"
                                                    class="form-select">
                                                    @foreach ($commandes as $cmd)
                                                        @foreach ($cmd->detailCommandeAchat as $d)
                                                            <option value="{{ $d->idDetailCom }}"
                                                                {{ $det->idDetailCom == $d->idDetailCom ? 'selected' : '' }}>
                                                                {{ $d->produit->libelle ?? 'Produit #' . $d->idDetailCom }}
                                                            </option>
                                                        @endforeach
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="details[{{ $i }}][qteReceptionne]"
                                                    class="form-control" value="{{ $det->qteReceptionne }}">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01"
                                                    name="details[{{ $i }}][prixUnit]" class="form-control"
                                                    value="{{ $det->prixUnit }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="this.closest('tr').remove()">
                                                    &times;
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <button type="button" class="btn btn-secondary"
                                onclick="addEditReceptionLine('{{ $rec->idRecep }}')">
                                Ajouter une ligne
                            </button>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach


    {{-- ================================
     Modals Suppression des réceptions
================================ --}}
    @foreach ($receptions as $rec)
        <div class="modal fade" id="deleteReceptionModal{{ $rec->idRecep }}" tabindex="-1"
            aria-labelledby="deleteReceptionLabel{{ $rec->idRecep }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteReceptionLabel{{ $rec->idRecep }}">
                            Supprimer réception
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            Êtes-vous sûr de vouloir supprimer la réception de la commande
                            <strong>{{ $rec->commandeAchat->reference }}</strong> ?
                        </p>
                        <p class="text-danger">Action irréversible.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <form action="{{ route('receptions.destroy', $rec->idRecep) }}" method="POST"
                            style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach


    {{-- ================================
     Script JS pour ajouter des lignes en édition
================================ --}}
    @push('scripts')
        <script>
            const editCounters = {};

            function addEditReceptionLine(recId) {
                if (!(recId in editCounters)) {
                    editCounters[recId] = document.querySelectorAll(`#editReceptionLines${recId} tr`).length;
                }
                const idx = editCounters[recId]++;
                const tbody = document.getElementById(`editReceptionLines${recId}`);
                const tr = document.createElement('tr');
                tr.innerHTML = `
      <td>
        <select name="details[${idx}][idDetailCom]" class="form-select">
          <option value="">-- sélectionner --</option>
          @foreach ($commandes as $cmd)
            @foreach ($cmd->detailCommandeAchat as $d)
              <option value="{{ $d->idDetailCom }}">
                {{ $d->produit->libelle ?? 'Produit #' . $d->idDetailCom }}
              </option>
            @endforeach
          @endforeach
        </select>
      </td>
      <td><input type="number" name="details[${idx}][qteReceptionne]" class="form-control"></td>
      <td><input type="number" step="0.01" name="details[${idx}][prixUnit]" class="form-control"></td>
      <td>
        <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">
          &times;
        </button>
      </td>`;
                tbody.appendChild(tr);
            }
        </script>
    @endpush

@endsection
