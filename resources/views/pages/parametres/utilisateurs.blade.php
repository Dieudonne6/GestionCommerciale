@extends('layouts.master')

@section('content')
    <div class="container py-4">
        <!-- Carte de gestion des utilisateurs -->
        <div class="card shadow-sm">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Gestion des utilisateurs</h4>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fa-solid fa-plus me-1"></i> Ajouter un utilisateur
                        </button>
                    </div>
                </div>
                <!-- Messages flash -->
                @if (Session::has('status'))
                    <div class="alert alert-success alert-dismissible">
                        {{ Session::get('status') }}
                    </div>
                @endif
                @if (Session::has('erreur'))
                    <div class="alert alert-danger alert-dismissible">
                        {{ Session::get('erreur') }}
                    </div>
                @endif
            </div>
            <div class="card-body">
                <!-- Tableau des utilisateurs -->
                <div class="table-responsive">
                    <table class="table mb-0" id="datatable_1">
                        <thead class="table-light">
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
                                <tr class="text-center">
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

                                <!-- Modal de modification -->
                                <div class="modal fade" id="editUserModal-{{ $user->idU }}" tabindex="-1"
                                    aria-labelledby="editUserModalLabel-{{ $user->idU }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <form action="{{ route('utilisateurs.modifie', $user->idU) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editUserModalLabel-{{ $user->idU }}">
                                                        Modifier utilisateur</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="nom-{{ $user->idU }}" class="form-label">Nom
                                                                complet</label>
                                                            <input type="text" name="nom"
                                                                class="form-control @error('nom') is-invalid @enderror"
                                                                value="{{ old('nom', $user->nom) }}" required>
                                                            @error('nom')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="idE-{{ $user->idU }}"
                                                                class="form-label">Entreprise</label>
                                                            <select name="idE"
                                                                class="form-select @error('idE') is-invalid @enderror"
                                                                required>
                                                                @foreach ($entreprises as $entreprise)
                                                                    <option value="{{ $entreprise->idE }}"
                                                                        {{ $user->idE == $entreprise->idE ? 'selected' : '' }}>
                                                                        {{ $entreprise->nom }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('idE')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="adresse-{{ $user->idU }}"
                                                                class="form-label">Adresse</label>
                                                            <input type="text" name="adresse"
                                                                class="form-control @error('adresse') is-invalid @enderror"
                                                                value="{{ old('adresse', $user->adresse) }}" required>
                                                            @error('adresse')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="telephone-{{ $user->idU }}"
                                                                class="form-label">Téléphone</label>
                                                            <input type="text" name="telephone"
                                                                class="form-control @error('telephone') is-invalid @enderror"
                                                                value="{{ old('telephone', $user->telephone) }}"
                                                                pattern="^\+?[0-9]{10,15}$"
                                                                title="Entrez un numéro de téléphone valide" required>
                                                            @error('telephone')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="mail-{{ $user->idU }}"
                                                                class="form-label">Email</label>
                                                            <input type="email" name="mail"
                                                                class="form-control @error('mail') is-invalid @enderror"
                                                                value="{{ old('mail', $user->mail) }}" required>
                                                            @error('mail')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="idRole-{{ $user->idU }}"
                                                                class="form-label">Rôle</label>
                                                            <select name="idRole"
                                                                class="form-select @error('idRole') is-invalid @enderror"
                                                                required>
                                                                @foreach ($roles as $role)
                                                                    <option value="{{ $role->idRole }}"
                                                                        {{ $user->idRole == $role->idRole ? 'selected' : '' }}>
                                                                        {{ $role->libelle }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('idRole')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Fermer</button>
                                                    <button type="submit"
                                                        class="btn btn-outline-warning">Modifier</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal de suppression -->
                                <div class="modal fade" id="deleteUserModal-{{ $user->idU }}" tabindex="-1"
                                    aria-labelledby="deleteUserModalLabel-{{ $user->idU }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('utilisateurs.supprime', $user->idU) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class="modal-header">
                                                    <h5 class="modal-title"
                                                        id="deleteUserModalLabel-{{ $user->idU }}">
                                                        Supprimer utilisateur</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Voulez-vous vraiment supprimer cet utilisateur ?
                                                </div>
                                                <div class="modal-footer d-flex justify-content-between">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit"
                                                        class="btn btn-outline-danger">Supprimer</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Fin du tableau -->
            </div>
        </div>
    </div>
    <!-- Modal d'ajout -->
    <div class="modal fade @if ($errors->any()) show @endif" id="addUserModal" tabindex="-1"
        aria-labelledby="addUserModalLabel" @if ($errors->any()) style="display: block;" @endif>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Ajouter un utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('utilisateurs.enregistre') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <!-- Regroupement des champs -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom complet</label>
                                <input type="text" name="nom"
                                    class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom') }}"
                                    required>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="idE" class="form-label">Entreprise</label>
                                <select name="idE" class="form-select @error('idE') is-invalid @enderror" required>
                                    <option value="" disabled selected>Sélectionner une entreprise</option>
                                    @foreach ($entreprises as $entreprise)
                                        <option value="{{ $entreprise->idE }}"
                                            {{ old('idE') == $entreprise->idE ? 'selected' : '' }}>
                                            {{ $entreprise->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('idE')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="adresse" class="form-label">Adresse</label>
                                <input type="text" name="adresse"
                                    class="form-control @error('adresse') is-invalid @enderror"
                                    value="{{ old('adresse') }}" required>
                                @error('adresse')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="text" name="telephone"
                                    class="form-control @error('telephone') is-invalid @enderror"
                                    pattern="^\+?[0-9]{10,15}$" title="Entrez un numéro de téléphone valide"
                                    value="{{ old('telephone') }}" required>
                                @error('telephone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mail" class="form-label">Email</label>
                                <input type="email" name="mail"
                                    class="form-control @error('mail') is-invalid @enderror" value="{{ old('mail') }}"
                                    required>
                                @error('mail')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="idRole" class="form-label">Rôle</label>
                                <select name="idRole" class="form-select @error('idRole') is-invalid @enderror"
                                    required>
                                    <option value="" disabled selected>Sélectionner un rôle</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->idRole }}"
                                            {{ old('idRole') == $role->idRole ? 'selected' : '' }}>
                                            {{ $role->libelle }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('idRole')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                <input type="password" name="password_confirmation"
                                    class="form-control @error('password_confirmation') is-invalid @enderror" required>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
@endsection

{{-- Scripts pour afficher automatiquement les modals en cas d'erreur --}}
@if (session('showModifyUserModal'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var userId = "{{ session('showModifyUserModal') }}";
            var existingModal = document.querySelector('.modal.show');
            if (existingModal) {
                var modalInstance = bootstrap.Modal.getInstance(existingModal);
                modalInstance.hide();
            }
            var myModalElement = document.getElementById('editUserModal-' + userId);
            var myModal = new bootstrap.Modal(myModalElement);
            setTimeout(() => {
                myModal.show();
            }, 300);
        });
    </script>
@endif

@if (session('showAddUserModal'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var myModal = new bootstrap.Modal(document.getElementById('addUserModal'));
            myModal.show();
        });
    </script>
@endif

@section('styles')
    <style>
        #datatable_1 td,
        #datatable_1 th {
            text-align: center;
        }

        #datatable_1 td img {
            display: block;
            margin: 0 auto;
        }

        #datatable_1 td a {
            display: inline-block;
            text-align: center;
        }

        .modal-content {
            border-radius: 8px;
        }
    </style>
@endsection
