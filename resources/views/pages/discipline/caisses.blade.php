@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h1>Gestion des Caisses</h1>

                {{-- Notifications --}}
                @if (session('success'))
                    <div class="alert alert-success" id="successMessage">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Bouton pour ouvrir le modal d'ajout --}}
                <div class="col-auto" style="text-align: right;">
                    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Ajouter une
                        Caisse</button>
                </div>

                {{-- Modal pour l'ajout --}}
                <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
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
                                    <input type="text" name="codeCais"
                                        class="form-control @error('codeCais') is-invalid @enderror"
                                        placeholder="Code de la caisse" required>
                                    @error('codeCais')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="libelleCais">Libellé de la Caisse</label>
                                    <input type="text" name="libelleCais"
                                        class="form-control @error('libelleCais') is-invalid @enderror"
                                        placeholder="Libellé de la caisse" required>
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
                <table class="table-responsive table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Libellé</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($caisses as $caisse)
                            <tr>
                                <td>{{ $caisse->codeCais }}</td>
                                <td>{{ $caisse->libelleCais }}</td>
                                <td class="text-end">
                                    {{-- Bouton de modification --}}
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $caisse->idCais }}">Modifier</button>

                                    {{-- Bouton de suppression --}}
                                    <!-- Bouton pour ouvrir le modal de confirmation -->
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $caisse->idCais }}">Supprimer</button>
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

    {{-- Modal de modification --}}
    <div class="modal fade" id="editModal{{ $caisse->idCais }}" tabindex="-1"
        aria-labelledby="editModalLabel{{ $caisse->idCais }}" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('caisses.update', $caisse->idCais) }}" method="POST" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel{{ $caisse->idCais }}">Modifier la Caisse</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="codeCais">Code</label>
                        <input type="text" name="codeCais" class="form-control @error('codeCais') is-invalid @enderror"
                            value="{{ $caisse->codeCais }}" required>
                        @error('codeCais')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="libelleCais">Libellé</label>
                        <input type="text" name="libelleCais"
                            class="form-control @error('libelleCais') is-invalid @enderror"
                            value="{{ $caisse->libelleCais }}" required>
                        @error('libelleCais')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Sauvegarder</button>
                </div>
            </form>
        </div>
    </div>
    {{-- Modal de confirmation de suppression --}}
    <div class="modal fade" id="deleteModal{{ $caisse->idCais }}" tabindex="-1"
        aria-labelledby="deleteModalLabel{{ $caisse->idCais }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel{{ $caisse->idCais }}">
                        Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer cette caisse ? Cette action est
                    irréversible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <!-- Formulaire de suppression -->
                    <form action="{{ route('caisses.destroy', $caisse->idCais) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
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
