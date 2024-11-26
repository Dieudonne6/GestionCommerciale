@extends('layouts.master')

@section('content')
<div class="container">
    <h1 class="my-4">Gestion des utilisateurs</h1>

    <!-- Notifications -->
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Liste des utilisateurs -->
    <div class="card">
        <div class="card-header">Liste des utilisateurs</div>
        <div class="card-body">
                <!-- Bouton pour ouvrir le modal d'ajout -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
        Ajouter un utilisateur
    </button>

    <!-- Modal Ajout -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('users.enregistre') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Ajouter un utilisateur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="login" class="form-label">Login</label>
                            <input type="text" name="login" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nomU" class="form-label">Nom complet</label>
                            <input type="text" name="nomU" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="adresseU" class="form-label">Adresse</label>
                            <input type="text" name="adresseU" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="text" name="telephone" class="form-control" pattern="^\+?[0-9]{10,15}$" title="Entrez un numéro de téléphone valide" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" name="password" class="form-control" minlength="6" required>
                        </div>
                        <div class="mb-3">
                            <label for="roleID" class="form-label">Rôle</label>
                            <select name="roleID" class="form-select" required>
                                <option value="1">Admin</option>
                                <option value="2">Utilisateur</option>
                            </select>
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
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Login</th>
                        <th>Nom</th>
                        <th>Adresse</th>
                        <th>Téléphone</th>
                        <th>Rôle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $user->login }}</td>
                        <td>{{ $user->nomU }}</td>
                        <td>{{ $user->adresseU }}</td>
                        <td>{{ $user->telephone }}</td>
                        <td>{{ $user->roleID == 1 ? 'Admin' : 'Utilisateur' }}</td>
                        <td>
                            <!-- Bouton Modifier -->
                            <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editUserModal-{{ $user->idU }}">
                                Modifier
                            </button>

                            <!-- Bouton Supprimer -->
                            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                data-bs-target="#deleteUserModal-{{ $user->idU }}">
                                Supprimer
                            </button>
                        </td>
                    </tr>

                    <!-- Modal Modification -->
                    <div class="modal fade" id="editUserModal-{{ $user->idU }}" tabindex="-1"
                        aria-labelledby="editUserModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('users.modifie', $user->idU) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editUserModalLabel">Modifier utilisateur</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="login" class="form-label">Login</label>
                                            <input type="text" name="login" value="{{ $user->login }}"
                                                class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="nomU" class="form-label">Nom complet</label>
                                            <input type="text" name="nomU" value="{{ $user->nomU }}"
                                                class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="adresseU" class="form-label">Adresse</label>
                                            <input type="text" name="adresseU" value="{{ $user->adresseU }}"
                                                class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="telephone" class="form-label">Téléphone</label>
                                            <input type="text" name="telephone" value="{{ $user->telephone }}"
                                                class="form-control" pattern="^\+?[0-9]{10,15}$" title="Entrez un numéro de téléphone valide" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="roleID" class="form-label">Rôle</label>
                                            <select name="roleID" class="form-select" required>
                                                <option value="1" {{ $user->roleID == 1 ? 'selected' : '' }}>Admin</option>
                                                <option value="2" {{ $user->roleID == 2 ? 'selected' : '' }}>Utilisateur</option>
                                            </select>
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
                                            data-bs-dismiss="modal">Annuler</button>
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
</div>
@endsection
