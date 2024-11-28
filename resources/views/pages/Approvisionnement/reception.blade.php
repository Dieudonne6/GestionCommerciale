@extends('layouts.master')
@section('content')
    <div class="container-xxl">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Réception</h4>
                            </div><!--end col-->
                        </div><!--end row-->
                    </div><!--end card-header-->
                    <div class="card-body pt-0">
                        <form method="POST" action="{{ route('handleReception') }}">
                            @csrf
                            <div class="d-flex flex-wrap justify-content-between">
                                <div class="d-flex flex-column mx-4" style="flex: 1; min-width: 200px;">
                                    <label for="typeC">Commande</label>
                                    <select name="typeC" id="typeC" class="form-select" style="width: 100%;" required>
                                        <option value="" disabled selected>Choisir une commande</option>
                                        @foreach ($commandes as $commande)
                                            <option value="{{ $commande->idCmd }}">{{ $commande->numCmd }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="d-flex flex-column mx-4" style="flex: 1; min-width: 200px;">
                                    <label for="dateC">Date de réception</label>
                                    <input type="date" name="dateC" id="dateC" class="form-control" style="width: 100%;" required>
                                </div>
                            </div>
                            <br>
                            <div class="d-flex flex-wrap justify-content-between">
                                <div class="d-flex flex-column mx-4" style="flex: 1; min-width: 200px;">
                                    <label for="referenceC">Référence BL</label>
                                    <input type="text" name="referenceC" id="referenceC" class="form-control" style="width: 100%;" required>
                                </div>
                                <div class="d-flex flex-column mx-4" style="flex: 1; min-width: 200px;">
                                    <label for="magasin">Magasin</label>
                                    <select name="magasin" id="magasin" class="form-select" required>
                                        <option value="" disabled selected>Choisir un magasin</option>
                                        @foreach ($magasins as $magasin)
                                            <option value="{{ $magasin->idMgs }}">{{ $magasin->libelleMgs }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary justify-content-end" style="margin-left: 1000px; margin-top: 20px;">Générer</button>
                        </form>
                        <hr>
                        <h5>Détails sur l'entrée de l'article</h5>
                        <table class="table table-bordered table-striped ">
                            <thead>
                                <tr>
                                    <th>Article</th>
                                    <th>Quantité</th>
                                    <th>Prix Unitaire</th>
                                    <th>Quantité restante</th>
                                    <th>Montant TTC</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($commandes as $commande)
                                    @foreach ($commande->lignesCommandes as $ligne)
                                        <tr>
                                            <td>{{ $ligne->produit->nom }}</td>
                                            <td>{{ $ligne->quantite }}</td>
                                            <td>{{ $ligne->prix_unitaire }}</td>
                                            <td>{{ $ligne->quantite - $ligne->quantite_recue }}</td>
                                            <td>{{ $ligne->quantite * $ligne->prix_unitaire }}</td>
                                            <td>
                                                <input type="number" name="quantite_{{ $ligne->id }}" min="0" max="{{ $ligne->quantite - $ligne->quantite_recue }}" class="form-control" required>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div><!--end card-->
            </div>
        </div>
    </div>
@endsection
