@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Gestion des Caisses</h4>
                    </div><!--end col-->
                    <div class="col-auto">
                        <div class="col-auto">
                            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="fa-solid fa-plus me-1"></i>
                                Ajouter une Caisse </button>
                        </div><!--end col-->
                    </div><!--end col-->
                </div><!--end row-->
            </div>

            <div class="card-body pt-0">
                {{-- Notifications --}}
                @if ($errors->any())
                    <div class="alert alert-danger" id="error-message">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success" id="success-message">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Modal pour l'ajout --}}
                <div class="modal" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('caisses.store') }}" method="POST" class="modal-content">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="addModalLabel">Ajouter une Caisse</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group mb-3">
                                    <label for="codeCais">Code de la Caisse</label>
                                    <input type="text" id="codeCais" name="codeCais"
                                        class="form-control @error('codeCais') is-invalid @enderror"
                                        placeholder="Code de la caisse" 
                                        value="{{ old('codeCais') }}" 
                                        required autofocus>
                                    @error('codeCais')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="libelleCais">Libellé de la Caisse</label>
                                    <input type="text" id="libelleCais" name="libelleCais"
                                        class="form-control @error('libelleCais') is-invalid @enderror"
                                        placeholder="Libellé de la caisse" 
                                        value="{{ old('libelleCais') }}" 
                                        required>
                                    @error('libelleCais')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Ajouter</button>
                            </div>
                        </form>
                    </div>
                </div>                

                {{-- Tableau des caisses --}}
                <table class="table-responsive table mb-0" id="datatable_1" >
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Libellé</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($caisses as $caisse)
                            <tr>
                                <td>{{ $caisse->codeCais }}</td>
                                <td>{{ $caisse->libelleCais }}</td>
                                <td class="text-center">
                                    {{-- Bouton de modification --}}
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $caisse->idCais }}">Modifier</button>

                                    {{-- Modal de modification --}}
                                    <div class="modal" id="editModal{{ $caisse->idCais }}" tabindex="-1"
                                        aria-labelledby="editModalLabel{{ $caisse->idCais }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form action="{{ route('caisses.update', $caisse->idCais) }}" method="POST"
                                                class="modal-content">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel{{ $caisse->idCais }}">
                                                        Modifier la Caisse</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="codeCais">Code de la caisse</label>
                                                        <input type="text" name="codeCais"
                                                            class="form-control @error('codeCais') is-invalid @enderror"
                                                            value="{{ $caisse->codeCais }}" required>
                                                        @error('codeCais')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="libelleCais">Libellé de la caisse</label>
                                                        <input type="text" name="libelleCais"
                                                            class="form-control @error('libelleCais') is-invalid @enderror"
                                                            value="{{ $caisse->libelleCais }}" required>
                                                        @error('libelleCais')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-success">Sauvegarder</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Bouton pour ouvrir le modal de confirmation de suppression -->
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $caisse->idCais }}">Supprimer</button>

                                    {{-- Modal de confirmation de suppression --}}
                                    <div class="modal" id="deleteModal{{ $caisse->idCais }}" tabindex="-1"
                                        aria-labelledby="deleteModalLabel{{ $caisse->idCais }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $caisse->idCais }}">
                                                        Confirmer la suppression</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Êtes-vous sûr de vouloir supprimer cette caisse ?
                                                    Cette action est irréversible.
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Annuler</button>
                                                    <!-- Formulaire de suppression -->
                                                    <form action="{{ route('caisses.destroy', $caisse->idCais) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Aucune caisse trouvée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Ajoutez ce script pour masquer automatiquement les messages -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Masquer les messages après 3 secondes
            setTimeout(function() {
                const successMessage = document.getElementById('success-message');
                const errorMessage = document.getElementById('error-message');
                if (successMessage) {
                    successMessage.style.display = 'none';
                }
                if (errorMessage) {
                    errorMessage.style.display = 'none';
                }
            }, 3000); // 3000ms = 3 secondes
        });
    </script>

@endsection
