@extends('layouts.master')
@section('content')
    <div class="container-xxl">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Réception de commande d'achat</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('receptions.store') }}" id="receptionForm">
                            @csrf
                            <div class="d-flex flex-wrap justify-content-between">
                                <div class="d-flex flex-column mx-4" style="flex: 1; min-width: 200px;">
                                    <label for="idCommande">Commande</label>
                                    <select name="idCommande" id="idCommande" class="form-select" required>
                                        <option value="" disabled selected>Choisir une commande</option>
                                        @foreach ($commandes as $commande)
                                            <option value="{{ $commande->idCommande }}">{{ $commande->reference }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="d-flex flex-column mx-4" style="flex: 1; min-width: 200px;">
                                    <label for="dateC">Date de réception</label>
                                    <input type="date" name="dateC" id="dateC" class="form-control"
                                        value="{{ old('dateC', date('Y-m-d')) }}" required>
                                </div>
                            </div>
                            <br>
                            <div class="d-flex flex-wrap justify-content-between">
                                <div class="d-flex flex-column mx-4" style="flex: 1; min-width: 200px;">
                                    <label for="referenceC">Référence BL</label>
                                    <input type="text" name="referenceC" id="referenceC" class="form-control"
                                        value="{{ old('referenceC') }}" required>
                                </div>
                                <div class="d-flex flex-column mx-4" style="flex: 1; min-width: 200px;">
                                    <label for="numBordereauLivraison">Numéro bordereau</label>
                                    <input type="text" name="numBordereauLivraison" id="numBordereauLivraison"
                                        class="form-control" value="{{ old('numBordereauLivraison') }}" required>
                                </div>
                                <div class="d-flex flex-column mx-4" style="flex: 1; min-width: 200px;">
                                    <label for="idExercice">Exercice</label>
                                    <select name="idExercice" id="idExercice" class="form-select" required>
                                        <option value="" disabled selected>Choisir un exercice</option>
                                        @foreach ($exercices as $exercice)
                                            <option value="{{ $exercice->idExercice }}"
                                                {{ old('idExercice') == $exercice->idExercice ? 'selected' : '' }}>
                                                {{ $exercice->libelleExercice }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <h5>Détails de la réception</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Article</th>
                                            <th>Quantité commandée</th>
                                            <th>Quantité déjà reçue</th>
                                            <th>Quantité à recevoir</th>
                                            <th>Prix unitaire</th>
                                            <th>Quantité reçue</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detailsCommande">
                                        @foreach ($commandes as $commande)
                                            @foreach ($commande->lignes as $detail)
                                                <tr class="detail-row" data-commande="{{ $commande->idCommande }}"
                                                    style="display: none;">
                                                    <td>{{ $detail->produit->NomP }}</td>
                                                    <td>{{ $detail->qte }}</td>
                                                    <td>{{ $detail->qteRecue }}</td>
                                                    <td>{{ $detail->qteRestante }}</td>
                                                    <td>{{ number_format($detail->prixUn, 2) }}</td>
                                                    <td>
                                                        <input type="number"
                                                            name="lignes[{{ $detail->idDetailCom }}][qteReceptionne]"
                                                            class="form-control qteReceptionne" min="0"
                                                            max="{{ $detail->qteRestante }}"
                                                            required>
                                                        <input type="hidden"
                                                            value="{{ $detail->idDetailCom }}">
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-primary float-end mt-3">
                                <i class="fas fa-save me-1"></i> Enregistrer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectCommande = document.getElementById('idCommande');
            const detailsCommande = document.getElementById('detailsCommande');
            const form = document.getElementById('receptionForm');

            // Afficher les détails de la commande sélectionnée
            selectCommande.addEventListener('change', function() {
                const commandeId = this.value;
                document.querySelectorAll('.detail-row').forEach(row => {
                    row.style.display = row.dataset.commande === commandeId ? '' : 'none';
                });
            });

            // Validation des quantités
            form.addEventListener('submit', function(e) {
                let isValid = true;
                const qteInputs = document.querySelectorAll('.qteReceptionne');

                qteInputs.forEach(input => {
                    const maxQte = parseFloat(input.max);
                    const value = parseFloat(input.value);

                    if (value > maxQte) {
                        isValid = false;
                        input.classList.add('is-invalid');
                        alert(
                            `La quantité reçue ne peut pas dépasser ${maxQte} pour l'article ${input.closest('tr').querySelector('td').textContent}`
                        );
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                }
            });

            // Validation en temps réel des quantités
            document.querySelectorAll('.qteReceptionne').forEach(input => {
                input.addEventListener('input', function() {
                    const maxQte = parseFloat(this.max);
                    const value = parseFloat(this.value);

                    if (value > maxQte) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                });
            });
        });
    </script>
@endsection