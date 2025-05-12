@extends('layouts.master')
@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Liste des réceptions</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReceptionModal">
                        <i class="fa-solid fa-plus me-1"></i> Nouvelle réception
                    </button>
                </div>

                @if (session('status') || session('erreur'))
                    <div class="alert alert-{{ session('status') ? 'success' : 'danger' }} alert-dismissible fade show">
                        {{ session('status') ?? session('erreur') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

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
                                    <td>
                                        <span class="badge bg-{{ $rec->statutRecep === 'complète' ? 'success' : ($rec->statutRecep === 'en cours' ? 'warning' : 'info') }}">
                                            {{ ucfirst($rec->statutRecep) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#editReceptionModal{{ $rec->idRecep }}"
                                            title="Modifier" {{ $rec->statutRecep === 'complète' ? 'disabled' : '' }}>
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteReceptionModal{{ $rec->idRecep }}"
                                            title="Supprimer" {{ $rec->statutRecep === 'complète' ? 'disabled' : '' }}>
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

{{-- Modal Nouvelle Réception --}}
<div class="modal fade" id="addReceptionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('receptions.store') }}" id="addReceptionForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nouvelle réception</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="errorContainer" class="alert alert-danger d-none">
                        <ul id="errorList"></ul>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Commande <span class="text-danger">*</span></label>
                            <select name="idCommande" id="selectCommande" class="form-select" required>
                                <option value="">-- choisir --</option>
                                @foreach ($commandes as $cmd)
                                    <option value="{{ $cmd->idCommande }}">
                                        {{ $cmd->reference }} — {{ $cmd->fournisseur->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date réception <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="date" class="form-control" required
                                value="{{ date('Y-m-d\TH:i') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Référence <span class="text-danger">*</span></label>
                            <input type="text" name="reference" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">N° bordereau livraison <span class="text-danger">*</span></label>
                            <input type="text" name="numBordereauLivraison" class="form-control" required>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Produit</th>
                                    <th>Qté commandée</th>
                                    <th>Qté restante</th>
                                    <th>Qté réceptionnée</th>
                                    <th>Prix unitaire</th>
                                    <th>Magasin</th>
                                </tr>
                            </thead>
                            <tbody id="detailsTableBody"></tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fa-solid fa-save me-1"></i> Enregistrer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#datatable_receptions').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json' },
            order: [[1, 'desc']]
        });

        const selectCommande = document.getElementById('selectCommande');
        const detailsTableBody = document.getElementById('detailsTableBody');
        const errorContainer = document.getElementById('errorContainer');
        const errorList = document.getElementById('errorList');
        const submitBtn = document.getElementById('submitBtn');

        function showErrors(errors) {
            errorList.innerHTML = '';
            errors.forEach(err => {
                const li = document.createElement('li');
                li.textContent = err;
                errorList.appendChild(li);
            });
            errorContainer.classList.remove('d-none');
        }

        function hideErrors() {
            errorContainer.classList.add('d-none');
            errorList.innerHTML = '';
        }

        selectCommande.addEventListener('change', function () {
            const id = this.value;
            if (!id) return detailsTableBody.innerHTML = '';

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Chargement...';

            fetch(`/receptions/commande-details/${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        detailsTableBody.innerHTML = '';
                        data.details.forEach((d, i) => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td><input type="hidden" name="details[${i}][idDetailCom]" value="${d.idDetailCom}">${d.produit}</td>
                                <td class="text-end">${d.qteCmd}</td>
                                <td class="text-end">${d.qteRestante}</td>
                                <td><input type="number" name="details[${i}][qteReceptionne]" class="form-control text-end" required min="1" max="${d.qteRestante}" value="${d.qteRestante}"></td>
                                <td><input type="number" name="details[${i}][prixUnit]" class="form-control text-end" required min="0" step="0.01" value="${d.prixUnit}"></td>
                                <td>
                                    <select name="details[${i}][idMagasin]" class="form-select" required>
                                        <option value="">-- Sélectionner --</option>
                                        @foreach ($magasins as $m)
                                            <option value="{{ $m->idMagasin }}">{{ $m->libelle }}</option>
                                        @endforeach
                                    </select>
                                </td>`;
                            detailsTableBody.appendChild(row);
                        });
                        hideErrors();
                    } else {
                        showErrors([data.message]);
                    }
                })
                .catch(() => showErrors(['Erreur lors du chargement des détails']))
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fa-solid fa-save me-1"></i> Enregistrer';
                });
        });
    });
</script>
@endpush
@endsection
