@extends('layouts.master')
@section('content')

    <style>
        .hidden{
            display: none;
        }
    </style>

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
                                                <span
                                                    class="badge bg-{{ $rec->statutRecep === 'complète' ? 'success' : ($rec->statutRecep === 'en cours' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($rec->statutRecep) }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info me-1" data-bs-toggle="modal"
                                                    data-bs-target="#detailReceptionModal{{ $rec->idRecep }}" title="Détails">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-primary me-1 hidden" data-bs-toggle="modal"
                                                    data-bs-target="#editReceptionModal{{ $rec->idRecep }}" title="Modifier"
                                                    {{ $rec->statutRecep === 'complète' ? 'disabled' : '' }}>
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteReceptionModal{{ $rec->idRecep }}"
                                                    title="Supprimer"
                                                    {{ $rec->statutRecep === 'complète' ? 'disabled' : '' }}>
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="deleteReceptionModal{{ $rec->idRecep }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirmation</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        Voulez-vous vraiment supprimer cette réception ?
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>

                                                        <form method="POST" action="{{ route('receptions.destroy', $rec->idRecep) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-danger">Supprimer</button>
                                                        </form>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>                                     

                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Modal détail Réception --}}
    @foreach ($receptions as $rec)
        <div class="modal fade" id="detailReceptionModal{{ $rec->idRecep }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Détails de la réception {{ $rec->reference }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Infos générales --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label>Date réception</label>
                                <input type="datetime-local" class="form-control" 
                                    value="{{ \Carbon\Carbon::parse($rec->date)->format('Y-m-d\TH:i') }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>Référence</label>
                                <input type="text" class="form-control" value="{{ $rec->reference }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>N° Bordereau</label>
                                <input type="text" class="form-control" value="{{ $rec->numBordereauLivraison }}" readonly>
                            </div>
                        </div>

                        {{-- Détails produits --}}
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Qté commandée</th>
                                        <th>Qté restante</th>
                                        <th>Qté réceptionnée</th>
                                        <th>Prix unitaire</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rec->detailReceptionCmdAchat as $i => $det)
                                        @php $dc = $det->detailCommandeAchat; @endphp
                                        <tr>
                                            <td>{{ $dc->produit->libelle }}</td>
                                            <td>{{ $dc->qteCmd }}</td>
                                            <td>{{ $dc->qteRestante }}</td>
                                            <td>{{ $det->qteReceptionne }}</td>
                                            <td>{{ $det->prixUnit }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

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
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif


                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Fournisseur <span class="text-danger">*</span></label>
                                <select name="idCommande" id="selectCommande" class="form-select" required>
                                    <option value="">-- choisir --</option>
                                    @foreach ($commandes as $cmd)
                                        <option 
                                            value="{{ $cmd->idCommande }}"
                                            data-reference="{{ $cmd->reference }}"
                                        >
                                            {{ $cmd->reference }} — {{ $cmd->fournisseur->nom }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date réception <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="date" class="form-control" required
                                    value="{{ date('Y-m-d\TH:i') }}">

                                @error('date')
                                  <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Référence <span class="text-danger">*</span></label>
                                <input type="text" name="reference" id="inputReference" class="form-control" readonly required>
                                @error('reference')
                                  <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">N° bordereau livraison <span class="text-danger">*</span></label>
                                <input type="text" name="numBordereauLivraison" class="form-control" required>

                                @error('numBordereauLivraison')
                                  <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                        <th>Qté restante après réception</th>
                                        <th class="d-none">Prix unitaire </th>
                                        <th>Date d'expiration (Si produit périssable)</th>
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

    {{-- Modal modification Réception --}}
    @foreach ($receptions as $rec)
        <div class="modal fade" id="editReceptionModal{{ $rec->idRecep }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form method="POST" action="{{ route('receptions.update', $rec->idRecep) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Modifier la réception {{ $rec->reference }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            {{-- Infos générales --}}
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label>Date réception</label>
                                    <input type="datetime-local" name="date"
                                        class="form-control"
                                        value="{{ \Carbon\Carbon::parse($rec->date)->format('Y-m-d\TH:i') }}"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label>Référence</label>
                                    <input type="text" name="reference" class="form-control"
                                        value="{{ $rec->reference }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label>N° Bordereau</label>
                                    <input type="text" name="numBordereauLivraison" class="form-control"
                                        value="{{ $rec->numBordereauLivraison }}" required>
                                </div>
                            </div>

                            {{-- Détails produits --}}
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Produit</th>
                                            <th>Qté commandée</th>
                                            <th>Qté restante</th>
                                            <th>Qté réceptionnée</th>
                                            <th>Prix unitaire</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rec->detailReceptionCmdAchat as $i => $det)
                                        @php $dc = $det->detailCommandeAchat; @endphp
                                        <tr>
                                            <td>{{ $dc->produit->libelle }}</td>
                                            <td>{{ $dc->qteCmd }}</td>
                                            <td>{{ $dc->qteRestante }}</td>
                                            <td>
                                                <input type="number" name="details[{{ $i }}][qteReceptionne]"
                                                    class="form-control" min="1"
                                                    max="{{ $dc->qteRestante }}"
                                                    value="{{ $det->qteReceptionne }}" required>
                                                <input type="hidden" name="details[{{ $i }}][idDetailCom]"
                                                    value="{{ $det->idDetailCom }}">
                                            </td>
                                            <td>
                                                <input type="number" name="details[{{ $i }}][prixUnit]"
                                                    class="form-control" value="{{ $det->prixUnit }}" readonly>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach


    @push('scripts')
        <script>
            document.getElementById('selectCommande').addEventListener('change', function () {

                const option = this.selectedOptions[0];
                const reference = option.getAttribute('data-reference');
                const idCommande = this.value;

                document.getElementById('inputReference').value = reference;

                if (!idCommande) return;

                fetch(`/receptions/commande-details/${idCommande}`)
                    .then(res => res.json())
                    .then(data => {

                        if (!data.success) {
                            alert(data.message);
                            return;
                        }

                        const tbody = document.getElementById('detailsTableBody');
                        tbody.innerHTML = '';

                        data.details.forEach((d, i) => {

                            const tr = document.createElement('tr');

                            tr.innerHTML = `
                                <td>${d.produit}</td>
                                <td>${d.qteCmd}</td>

                                <td>${d.qteRestante}</td>
                                <td>
                                    <input type="number"
                                        name="details[${i}][qteReceptionne]"
                                        class="form-control qteReceptionneInput"
                                        min="0"
                                        max="${d.qteRestante}"
                                        required>
                                    <input type="hidden"
                                        name="details[${i}][iddetailcom]"
                                        value="${d.idDetailCom}">
                                </td>
                                <td class="qteRestanteApres">${d.qteRestante}</td>
                                <td class="d-none">
                                    <input type="number"
                                        name="details[${i}][prixUnit]"
                                        class="form-control "
                                        value="${d.prixUnit}" readonly>
                                </td>
                                <td>
                                    <input type="date"
                                        name="details[${i}][expiration]"
                                        class="form-control expiration-input">
                                </td>
                                <td>
                                    <input type="hidden" name="details[${i}][idMag]" value="${d.idMag}">
                                    ${d.idMag}
                                </td>
                            `;

                            tbody.appendChild(tr);

                            const expirationInput = tr.querySelector('.expiration-input');
                            const alertInput = tr.querySelector('.alert-input');

                            expirationInput.addEventListener('change', function () {

                                if (this.value) {
                                    alertInput.classList.remove('d-none');
                                    alertInput.required = true;

                                    // l’alerte doit être AVANT expiration
                                    alertInput.max = this.value;

                                } else {
                                    alertInput.classList.add('d-none');
                                    alertInput.required = false;
                                    alertInput.value = '';
                                }
                            });

                            const input = tr.querySelector('.qteReceptionneInput');
                            const restanteCell = tr.querySelector('.qteRestanteApres');
                            const max = parseInt(d.qteRestante);

                            input.addEventListener('input', function () {

                                let val = parseInt(this.value || 0);

                                if (val > max) {
                                    alert(`La quantité restante a réceptionné est ${max}, vous ne pouvez donc réceptionner plus de ${max}.`);
                                    this.value = max;
                                    val = max;
                                }

                                if (val < 0) {
                                    this.value = 0;
                                    val = 0;
                                }

                                restanteCell.textContent = max - val;
                            });
                        });
                    });
            });
            
        </script>
@if (session('errorModalId'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = new bootstrap.Modal(
            document.getElementById('{{ session('errorModalId') }}')
        );
        modal.show();
    });
</script>
@endif


    @endpush
@endsection






