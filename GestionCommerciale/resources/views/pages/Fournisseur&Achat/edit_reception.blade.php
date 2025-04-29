@extends('layouts.master')
@section('content')
    <div class="container-xxl">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Modifier la commande d'achat</h4>
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

                        <form method="POST" action="{{ route('commandes.update', $commande->idCommande) }}"
                            id="commandeForm">
                            @csrf
                            @method('PUT')
                            <div class="d-flex flex-wrap justify-content-between">
                                <div class="d-flex flex-column mx-4" style="flex: 1; min-width: 200px;">
                                    <label for="dateC">Date de commande</label>
                                    <input type="date" name="dateC" id="dateC" class="form-control"
                                        value="{{ old('dateC', \Carbon\Carbon::parse($commande->date)->format('Y-m-d')) }}"
                                        required>
                                </div>
                                <div class="d-flex flex-column mx-4" style="flex: 1; min-width: 200px;">
                                    <label for="referenceC">Référence de commande</label>
                                    <input type="text" name="referenceC" id="referenceC" class="form-control"
                                        value="{{ old('referenceC', $commande->reference) }}" required>
                                </div>
                                <div class="d-flex flex-column mx-4" style="flex: 1; min-width: 200px;">
                                    <label for="numBordereauLivraison">Numéro bordereau</label>
                                    <input type="text" name="numBordereauLivraison" id="numBordereauLivraison"
                                        class="form-control"
                                        value="{{ old('numBordereauLivraison', $commande->numBordereauLivraison) }}"
                                        required>
                                </div>
                                <div class="d-flex flex-column mx-4" style="flex: 1; min-width: 200px;">
                                    <label for="idExercice">Exercice</label>
                                    <select name="idExercice" id="idExercice" class="form-select" required>
                                        <option value="" disabled>Choisir un exercice</option>
                                        @foreach ($exercices as $exercice)
                                            <option value="{{ $exercice->idExercice }}"
                                                {{ old('idExercice', $commande->idExercice) == $exercice->idExercice ? 'selected' : '' }}>
                                                {{ $exercice->libelleExercice }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <h5>Détails de la commande</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Article</th>
                                            <th>Quantité commandée</th>
                                            <th>Prix unitaire</th>
                                            <th>Quantité reçue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($commande->lignes as $detail)
                                            <tr>
                                                <td>{{ $detail->produit->NomP }}</td>
                                                <td>{{ $detail->qte }}</td>
                                                <td>{{ number_format($detail->prixUn, 2) }}</td>
                                                <td>
                                                    <input type="number"
                                                        name="lignes[{{ $detail->idDetailCom }}][qteReceptionne]"
                                                        class="form-control qteReceptionne" min="0"
                                                        value="{{ old('lignes.' . $detail->idDetailCom . '.qteReceptionne', $detail->qteReceptionne) }}"
                                                        required>
                                                    <input type="hidden"
                                                        name="lignes[{{ $detail->idDetailCom }}][idDetailCom]"
                                                        value="{{ $detail->idDetailCom }}">
                                                    <input type="hidden"
                                                        name="lignes[{{ $detail->idDetailCom }}][prixUnit]"
                                                        value="{{ $detail->prixUn }}">
                                                </td>
                                            </tr>
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
            const form = document.getElementById('commandeForm');

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