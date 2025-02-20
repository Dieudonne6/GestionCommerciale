@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="card mt-4">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Gestion des Rôles</h4>
                    </div><!--end col-->
                    <div class="col-auto">
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                                <i class="fa-solid fa-plus me-1"></i> Ajouter un rôle
                            </button>
                        </div><!--end col-->
                    </div><!--end col-->
                </div><!--end row-->
            </div>

            <div class="card-body">
                <!-- Message de succès -->
                @if (session('success'))
                    <div class="alert alert-success" id="successMessage">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Liste des rôles -->
                <h5>Liste des Rôles</h5>
                <table class="table-responsive table mb-0" id="datatable_1">
                    <thead class="table-light">
                        <tr>
                            <th>Rôles</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $role->libelle }}</td>
                                <td>
                                    <!-- Modifier -->
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editRoleModal{{ $role->idRole }}">Modifier</button>

                                    <!-- Supprimer -->
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#deleteRoleModal{{ $role->idRole }}">Supprimer</button>
                                </td>
                            </tr>

                            <!-- Modal de modification -->
                            <div class="modal fade" id="editRoleModal{{ $role->idRole }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('updateRole', $role->idRole) }}" method="POST">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Modifier le rôle</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="libelle{{ $role->idRole }}" class="form-label">Nom du rôle</label>
                                                    <input type="text" class="form-control"
                                                        id="libelle{{ $role->idRole }}" name="libelle"
                                                        value="{{ $role->libelle }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Annuler</button>
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
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Êtes-vous sûr de vouloir supprimer le rôle
                                                    <strong>{{ $role->libelle }}</strong> ?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Annuler</button>
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

        <!-- Modal d'ajout -->
        <div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
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
                                <input type="text" class="form-control" id="libelle" name="libelle" required>
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

    <script>
        setTimeout(function() {
            let successMessage = document.getElementById('successMessage');
            if (successMessage) {
                successMessage.style.display = 'none';
            }
        }, 3000); // Le message disparaît après 3 secondes (3000 ms)
    </script>
@endsection
