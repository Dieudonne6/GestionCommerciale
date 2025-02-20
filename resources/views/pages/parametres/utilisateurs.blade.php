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
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="card-title">Gestion des utilisateurs</h4>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addUserModal"><i class="fa-solid fa-plus me-1"></i> Ajouter un utilisateur</button>
                </div>
            </div>
        </div>
        <div class="card-body">

            <!-- Modal Ajout -->
            <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form action="{{ route('users.enregistre') }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="addUserModalLabel">Ajouter un utilisateur</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Regroupement des champs -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nom" class="form-label">Nom complet</label>
                                        <input type="text" name="nom" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="idE" class="form-label">Entreprise</label>
                                        <select name="idE" class="form-select" required>
                                            @foreach ($entreprises as $entreprise)
                                                <option value="{{ $entreprise->idE }}"
                                                    {{ $user->idE == $entreprise->idE ? 'selected' : '' }}>
                                                    {{ $entreprise->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="adresse" class="form-label">Adresse</label>
                                        <input type="text" name="adresse" class="form-control" required>
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
                                        <label for="mail" class="form-label">Email</label>
                                        <input type="email" name="mail" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="idRole" class="form-label">Rôle</label>
                                        <select name="idRole" class="form-select" required>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->idRole }}" {{ old('idRole') == $role->idRole ? 'selected' : '' }}>
                                                    {{ $role->libelle }}
                                                </option>
                                            @endforeach
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
                        <th>Nom</th>
                        <th>Adresse</th>
                        <th>Téléphone</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Entreprise</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($utilisateurs as $user)
                        <tr>
                            <td>{{ $user->nom }}</td>
                            <td>{{ $user->adresse }}</td>
                            <td>{{ $user->telephone }}</td>
                            <td>{{ $user->mail }}</td>
                            <td>
                                {{ $user->role->libelle ?? ($user->idRole == 1 ? 'Admin' : 'Utilisateur') }}
                            </td>
                            <td>
                                {{ $user->entreprise->nom ?? 'Non défini' }}
                            </td>
                            <td>
                                <!-- Bouton Modifier -->
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
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
                                                    <label for="nom" class="form-label">Nom complet</label>
                                                    <input type="text" name="nom" class="form-control"
                                                        value="{{ old('nom', $user->nom) }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="idE" class="form-label">Entreprise</label>
                                                    <select name="idE" class="form-select" required>
                                                        @foreach ($entreprises as $entreprise)
                                                            <option value="{{ $entreprise->idE }}"
                                                                {{ $user->idE == $entreprise->idE ? 'selected' : '' }}>
                                                                {{ $entreprise->nom }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>  
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="adresse" class="form-label">Adresse</label>
                                                    <input type="text" name="adresse" class="form-control"
                                                        value="{{ old('adresse', $user->adresse) }}" required>
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
                                                    <label for="mail" class="form-label">Email</label>
                                                    <input type="email" name="mail" class="form-control"
                                                        value="{{ old('mail', $user->mail) }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="idRole" class="form-label">Rôle</label>
                                                    <select name="idRole" class="form-select" required>
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role->idRole }}"
                                                                {{ $user->idRole == $role->idRole ? 'selected' : '' }}>
                                                                {{ $role->libelle }}
                                                            </option>
                                                        @endforeach
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
