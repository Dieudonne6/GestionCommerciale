@extends('layouts.master')

@section('content')
    <div class="container py-4">
        <!-- Notifications -->
        @if (session('success'))
        <div class="alert alert-success" id="successMessage">
            {{ session('success') }}
        </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Liste des utilisateurs -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="my-4">Gestion des utilisateurs</h4>
                {{-- Bouton pour ouvrir le modal d'ajout --}}
                <button class="btn btn-primary mb-3 ms-auto d-flex" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    Ajouter un utilisateur
                </button>
            </div>

            <!-- Modal Ajout -->
            <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg"> <!-- Larger modal dialog -->
                    <div class="modal-content">
                        <form action="{{ route('users.enregistre') }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="addUserModalLabel">Ajouter un utilisateur</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Group fields into row for better spacing -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="login" class="form-label">Login</label>
                                        <input type="text" name="login" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="nomU" class="form-label">Nom complet</label>
                                        <input type="text" name="nomU" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="adresseU" class="form-label">Adresse</label>
                                        <input type="text" name="adresseU" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="telephone" class="form-label">Téléphone</label>
                                        <input type="text" name="telephone" class="form-control"
                                            pattern="^\+?[0-9]{10,15}$" title="Entrez un numéro de téléphone valide"
                                            required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Mot de passe</label>
                                        <input type="password" name="password" class="form-control" minlength="6" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="roleID" class="form-label">Rôle</label>
                                        <select name="roleID" class="form-select" required>
                                            <option value="1">Admin</option>
                                            <option value="2">Utilisateur</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                <button type="submit" class="btn btn-primary">Ajouter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <table class="table-responsive table mb-0" id="datatable_1">
                <thead>
                    <tr>
                        <th>Login</th>
                        <th>Nom</th>
                        <th>Adresse</th>
                        <th>Téléphone</th>
                        <th>Rôle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->login }}</td>
                            <td>{{ $user->nomU }}</td>
                            <td>{{ $user->adresseU }}</td>
                            <td>{{ $user->telephone }}</td>
                            <td>{{ $user->roleID == 1 ? 'Admin' : 'Utilisateur' }}</td>
                            <td>
                                <!-- Bouton Modifier -->
                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editUserModal-{{ $user->idU }}">
                                    Modifier
                                </button>

                                <!-- Bouton Supprimer -->
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#deleteUserModal-{{ $user->idU }}">
                                    Supprimer
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Modification -->
                        <div class="modal fade" id="editUserModal-{{ $user->idU }}" tabindex="-1"
                            aria-labelledby="editUserModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="{{ route('users.modifie', $user->idU) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editUserModalLabel">Modifier utilisateur</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="login" class="form-label">Login</label>
                                                    <input type="text" name="login" class="form-control"
                                                        value="{{ old('login', $user->login) }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="nomU" class="form-label">Nom complet</label>
                                                    <input type="text" name="nomU" class="form-control"
                                                        value="{{ old('nomU', $user->nomU) }}" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="adresseU" class="form-label">Adresse</label>
                                                    <input type="text" name="adresseU" class="form-control"
                                                        value="{{ old('adresseU', $user->adresseU) }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="telephone" class="form-label">Téléphone</label>
                                                    <input type="text" name="telephone" class="form-control"
                                                        value="{{ old('telephone', $user->telephone) }}"
                                                        pattern="^\+?[0-9]{10,15}$"
                                                        title="Entrez un numéro de téléphone valide" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="password" class="form-label">Mot de passe</label>
                                                    <input type="password" name="password" class="form-control"
                                                        minlength="6" placeholder="Laisser vide si pas de changement">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="roleID" class="form-label">Rôle</label>
                                                    <select name="roleID" class="form-select" required>
                                                        <option value="1" {{ $user->roleID == 1 ? 'selected' : '' }}>
                                                            Admin</option>
                                                        <option value="2" {{ $user->roleID == 2 ? 'selected' : '' }}>
                                                            Utilisateur</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Fermer</button>
                                            <button type="submit" class="btn btn-outline-warning">Modifier</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>



                        <!-- Modal Suppression -->
                        <div class="modal fade" id="deleteUserModal-{{ $user->idU }}" tabindex="-1"
                            aria-labelledby="deleteUserModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('users.supprime', $user->idU) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteUserModalLabel">Supprimer utilisateur</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Voulez-vous vraiment supprimer cet utilisateur ?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Fermer</button>
                                            <button type="submit" class="btn btn-outline-danger">Supprimer</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        setTimeout(function() {
            let successMessage = document.getElementById('successMessage');
            if (successMessage) {
                successMessage.style.display = 'none';
            }
        }, 3000); // Le message disparaît après 3 secondes (3000 ms)
    </script>
@endsection
