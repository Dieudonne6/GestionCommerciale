@extends('layouts.master')
@section('content')
    <div class="container-xxl">
        <div class="card mt-4">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Gestion des Rôles</h4>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                            <i class="fa-solid fa-plus me-1"></i> Ajouter un rôle
                        </button>
                    </div>
                </div>
                <!-- Message flash de succès -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>

            <div class="card-body">
                <h5>Liste des Rôles</h5>
                <div class="table-responsive">
                    <table class="table mb-0" id="datatable_1">
                        <thead class="table-light">
                            <tr>
                                <th>Rôles</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                <tr class="text-center">
                                    <td>{{ $role->libelle }}</td>
                                    <td>
                                        <!-- Bouton de modification -->
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editRoleModal{{ $role->idRole }}">
                                            Modifier
                                        </button>
                                        <!-- Bouton de suppression -->
                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#deleteRoleModal{{ $role->idRole }}">
                                            Supprimer
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal de modification -->
                                <div class="modal fade" id="editRoleModal{{ $role->idRole }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ route('updateRole', $role->idRole) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Modifier le rôle</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="libelle{{ $role->idRole }}" class="form-label">Nom du rôle</label>
                                                        <input type="text" class="form-control @error('libelle') is-invalid @enderror"
                                                            id="libelle{{ $role->idRole }}" name="libelle"
                                                            value="{{ old('libelle', $role->libelle) }}" required>
                                                        @error('libelle')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
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

                                <!-- Modal de suppression -->
                                <div class="modal fade" id="deleteRoleModal{{ $role->idRole }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ route('deleteRole', $role->idRole) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Supprimer le rôle</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Êtes-vous sûr de vouloir supprimer le rôle <strong>{{ $role->libelle }}</strong> ?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal d'ajout -->
        <div class="modal fade @if ($errors->any()) show @endif" id="addRoleModal" tabindex="-1" aria-hidden="true" @if ($errors->any()) style="display: block;" @endif>
            <div class="modal-dialog">
                <form action="{{ route('storeRole') }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Ajouter un rôle</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="libelle" class="form-label">Nom du rôle</label>
                                <input type="text" class="form-control @error('libelle') is-invalid @enderror"
                                    id="libelle" name="libelle" value="{{ old('libelle') }}" required>
                                @error('libelle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Ajouter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

{{-- Script pour afficher automatiquement la modal d'ajout en cas d'erreur --}}
@if ($errors->any())
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var addRoleModal = new bootstrap.Modal(document.getElementById('addRoleModal'));
            addRoleModal.show();
        });
    </script>
@endif

{{-- Script pour masquer le message de succès après 3 secondes --}}
<script>
    setTimeout(function() {
        let successMessage = document.querySelector('.alert-success');
        if (successMessage) {
            successMessage.style.display = 'none';
        }
    }, 3000);
</script>

@section('styles')
    <style>
        /* Styles personnalisés pour les modals */
        .modal-header {
            background-color: #fff !important;
        }
        .modal-title {
            color: #000 !important;
        }
        #datatable_1 td,
        #datatable_1 th {
            text-align: center;
        }
        .modal-content {
            border-radius: 8px;
        }
    </style>
@endsection