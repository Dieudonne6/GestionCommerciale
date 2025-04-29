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

                        <form method="POST" action="{{ route('commandes.update', $commande->idCommande) }}" id="commandeForm">
                            @csrf
                            @method('PUT')
                            <div class="d-flex flex-wrap justify-content-between">
                                <div class="d-flex flex-column mx-4" style="flex: 1; min-width: 200px;">
                                    <label for="dateC">Date de commande</label>
                                    <input type="date" name="dateC" id="dateC" class="form-control"
                                        value="{{ old('dateC', \Carbon\Carbon::parse($commande->date)->format('Y-m-d')) }}" required>
                                </div>
                                <div class="d-flex flex-column mx-4" style="flex: 1; min-width: 200px;">
                                    <label for="referenceC">Référence de commande</label>
                                    <input type="text" name="referenceC" id="referenceC" class="form-control"
                                        value="{{ old('referenceC', $commande->reference) }}" required>
                                </div>
                                <div class="d-flex flex-column mx-4" style="flex: 1; min-width: 200px;">
                                    <label for="idFournisseur">Fournisseur</label>
                                    <select name="idFournisseur" id="idFournisseur" class="form-select" required>
                                        <option value="" disabled>Choisir un fournisseur</option>
                                        @foreach ($fournisseurs as $fournisseur)
                                            <option value="{{ $fournisseur->idFournisseur }}"
                                                {{ old('idFournisseur', $commande->idFournisseur) == $fournisseur->idFournisseur ? 'selected' : '' }}>
                                                {{ $fournisseur->nomFournisseur }}
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($commande->lignes as $detail)
                                            <tr>
                                                <td>{{ $detail->produit->NomP }}</td>
                                                <td>
                                                    <input type="number" name="lignes[{{ $detail->idDetailCom }}][qte]"
                                                        class="form-control" min="1" value="{{ old('lignes.' . $detail->idDetailCom . '.qte', $detail->qte) }}" required>
                                                </td>
                                                <td>{{ number_format($detail->prixUn, 2) }}</td>
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
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('commandeForm');

        // Validation des quantités
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const qteInputs = document.querySelectorAll('input[name^="lignes["][name$="[qte]"]');

            qteInputs.forEach(input => {
                const value = parseFloat(input.value);
                if (value < 1) {
                    isValid = false;
                    input.classList.add('is-invalid');
                    alert('La quantité doit être au moins 1 pour l\'article ' + input.closest('tr').querySelector('td').textContent);
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    });
</script>