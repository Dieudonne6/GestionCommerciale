@extends('layouts.master')
@section('content')
    <div class="container-xxl">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <!-- En-tête de la liste -->
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Liste des Clients</h4>
                            </div><!-- end col -->
                            <div class="col-auto">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#addClientModal">
                                    <i class="fa-solid fa-plus me-1"></i> Ajouter un Client
                                </button>
                            </div><!-- end col -->
                        </div><!-- end row -->
                    </div>

                    <!-- Affichage des messages flash -->
                    @if (Session::has('status'))
                        <div id="statusAlert" class="alert alert-success">
                            {{ Session::get('status') }}
                        </div>
                    @endif
                    @if (Session::has('erreur'))
                        <div id="statusAlert" class="alert alert-danger">
                            {{ Session::get('erreur') }}
                        </div>
                    @endif

                    <!-- Corps de la carte avec la table -->
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table mb-0" id="datatable_1">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-0">Nom & Prénoms</th>
                                        <th>Adresse</th>
                                        <th>Contact</th>
                                        <th>Email</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($clients as $client)
                                        <tr>
                                            <td>{{ $client->nom }}</td>
                                            <td>{{ $client->adresse }}</td>
                                            <td>{{ $client->telephone }}</td>
                                            <td>{{ $client->mail }}</td>
                                            <td class="text-end">
                                                <!-- Bouton de modification -->
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#editClientModal{{ $client->idC }}">
                                                    Modifier
                                                </button>
                                                <!-- Bouton de suppression -->
                                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteClientModal{{ $client->idC }}">
                                                    Supprimer
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Modal de modification -->
                                        <div class="modal fade" id="editClientModal{{ $client->idC }}" tabindex="-1"
                                            aria-labelledby="editClientModalLabel{{ $client->idC }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="editClientModalLabel{{ $client->idC }}">Modifier un Client
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('clients.update', $client->idC) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6 mb-2">
                                                                    <label>IFU</label>
                                                                    <input type="number" class="form-control"
                                                                        name="IFU" placeholder="IFU"
                                                                        value="{{ old('IFU', $client->IFU) }}" required>
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <label>Nom & Prénoms</label>
                                                                    <input type="text" class="form-control"
                                                                        name="nom" placeholder="Nom"
                                                                        value="{{ old('nom', $client->nom) }}" required>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-6 mb-2">
                                                                    <label>Adresse</label>
                                                                    <input type="text" class="form-control"
                                                                        name="adresse" placeholder="Adresse"
                                                                        value="{{ old('adresse', $client->adresse) }}"
                                                                        required>
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <label>Téléphone</label>
                                                                    <input type="text" class="form-control"
                                                                        name="telephone" placeholder="Téléphone"
                                                                        value="{{ old('telephone', $client->telephone) }}"
                                                                        required>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-6 mb-2">
                                                                    <label>Email</label>
                                                                    <input type="email" class="form-control"
                                                                        name="mail" placeholder="Email"
                                                                        value="{{ old('mail', $client->mail) }}" required>
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <label>Catégorie Client</label>
                                                                    <select class="form-control" name="idCatCl" required>
                                                                        <option value="" disabled>Sélectionner une
                                                                            catégorie</option>
                                                                        @foreach ($categories as $categorie)
                                                                            <option value="{{ $categorie->idCatCl }}"
                                                                                {{ old('idCatCl', $client->idCatCl) == $categorie->idCatCl ? 'selected' : '' }}>
                                                                                {{ $categorie->libelle }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Annuler</button>
                                                            <button type="submit" class="btn btn-primary">Modifier</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Modal de suppression -->
                                        <div class="modal fade" id="deleteClientModal{{ $client->idC }}" tabindex="-1"
                                            aria-labelledby="deleteClientModalLabel{{ $client->idC }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="deleteClientModalLabel{{ $client->idC }}">Confirmation de
                                                            suppression</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Êtes-vous sûr de vouloir supprimer ce client ?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('clients.destroy', $client->idC) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-danger">Confirmer</button>
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

    <!-- Modal d'ajout -->
    <div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addClientModalLabel">Ajouter un Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('clients.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <!-- Affichage des erreurs -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <!-- Champ IFU -->
                            <div class="col-md-6 mb-3">
                                <label for="ifu" class="form-label">IFU</label>
                                <input type="number" class="form-control" id="ifu" name="IFU"
                                    value="{{ old('IFU') }}" required>
                            </div>

                            <!-- Champ Nom & Prénoms -->
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom & Prénoms</label>
                                <input type="text" class="form-control" id="nom" name="nom"
                                    placeholder="Nom" value="{{ old('nom') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Champ Adresse -->
                            <div class="col-md-6 mb-3">
                                <label for="adresse" class="form-label">Adresse</label>
                                <input type="text" class="form-control" id="adresse" name="adresse"
                                    placeholder="Adresse" value="{{ old('adresse') }}" required>
                            </div>

                            <!-- Champ Téléphone -->
                            <div class="col-md-6 mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="text" class="form-control" id="telephone" name="telephone"
                                    placeholder="Téléphone" value="{{ old('telephone') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Champ Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="mail"
                                    placeholder="Email" value="{{ old('mail') }}" required>
                            </div>

                            <!-- Sélecteur Catégorie Client -->
                            <div class="col-md-6 mb-3">
                                <label for="idCatCl" class="form-label">Catégorie Client</label>
                                <select class="form-control" id="idCatCl" name="idCatCl" required>
                                    <option value="" disabled selected>Sélectionner une catégorie</option>
                                    @foreach ($categories as $categorie)
                                        <option value="{{ $categorie->idCatCl }}"
                                            {{ old('idCatCl') == $categorie->idCatCl ? 'selected' : '' }}>
                                            {{ $categorie->libelle }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Footer du modal -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var addClientModal = new bootstrap.Modal(document.getElementById('addClientModal'));
            @if ($errors->any())
                // Si des erreurs de validation existent lors de l'ajout, le modal s'ouvre automatiquement.
                addClientModal.show();
            @endif
        });
    </script>
@endsection
