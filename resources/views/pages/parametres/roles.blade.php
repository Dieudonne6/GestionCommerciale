@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="card mt-4">

            <div class="card-body">
                <h4>Gestion des Rôles</h4>
                <!-- Message de succès -->
                @if (session('success'))
                    <div class="alert alert-success" id="successMessage">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Bouton pour afficher le modal d'ajout -->
                <div class="text-end">
                    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addRoleModal">Ajouter un
                        rôle</button>
                </div>

                <!-- Liste des rôles -->
                <h5>Liste des Rôles</h5>
                <table class="table-responsive table mb-0" id="datatable_1">
                    <thead class="table-light">
                        <tr>
                            <th>Roles</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $role->libelleRole }}</td>
                                <td>
                                    <!-- Modifier -->
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editRoleModal{{ $role->id }}">Modifier</button>

                                    <!-- Supprimer -->
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#deleteRoleModal{{ $role->id }}">Supprimer</button>
                                </td>
                            </tr>

                            <!-- Modal de modification -->
                            <div class="modal fade" id="editRoleModal{{ $role->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('updateRole', $role->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Modifier le rôle</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="libelleRole{{ $role->id }}" class="form-label">Nom du
                                                        rôle</label>
                                                    <input type="text" class="form-control"
                                                        id="libelleRole{{ $role->id }}" name="libelleRole"
                                                        value="{{ $role->libelleRole }}" required>
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
                            <div class="modal fade" id="deleteRoleModal{{ $role->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('deleteRole', $role->id) }}" method="POST">
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
                                                    <strong>{{ $role->libelleRole }}</strong> ?</p>
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
                                <label for="libelleRole" class="form-label">Nom du rôle</label>
                                <input type="text" class="form-control" id="libelleRole" name="libelleRole" required>
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
