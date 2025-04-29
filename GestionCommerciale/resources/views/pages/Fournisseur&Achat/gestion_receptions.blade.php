@extends('layouts.master')

@section('content')
    <div class="container">
        <h1 class="mt-4">Gestion des Réceptions</h1>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="card mb-5 me-3">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Liste des réceptions</h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('receptions.create') }}" class="btn btn-primary">
                                <i class="fa-solid fa-plus me-1"></i> Ajouter une réception
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table mb-0" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th>Numéro</th>
                                    <th>Date</th>
                                    <th>Référence BL</th>
                                    <th>Numéro bordereau</th>
                                    <th>Commande</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($receptions as $reception)
                                    <tr>
                                        <td>{{ $reception->reference }}</td>
                                        <td>{{ \Carbon\Carbon::parse($reception->date)->format('d/m/Y') }}</td>
                                        <td>{{ $reception->reference }}</td>
                                        <td>{{ $reception->numBordereauLivraison }}</td>
                                        <td>{{ $reception->commandeAchat->numCmd }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $reception->statutRecep == 'complète' ? 'success' : 'warning' }}">
                                                {{ $reception->statutRecep }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <a href="{{ route('receptions.edit', $reception->idRecep) }}"
                                                    class="btn btn-warning me-2 {{ $reception->statutRecep == 'complète' ? 'disabled' : '' }}">
                                                    <i class="fas fa-edit me-1"></i> Modifier
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('receptions.destroy', $reception->idRecep) }}"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-danger {{ $reception->statutRecep == 'complète' ? 'disabled' : '' }}"
                                                        onclick="return confirm('Voulez-vous vraiment supprimer cette réception ?')">
                                                        <i class="fas fa-trash me-1"></i> Supprimer
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Aucune réception enregistrée.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialisation de DataTable
            $('#datatable_1').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
                },
                order: [
                    [1, 'desc']
                ], // Tri par date décroissante
                pageLength: 10,
                responsive: true
            });

            // Gestion des boutons désactivés
            document.querySelectorAll('.btn.disabled').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    alert('Cette action n\'est pas disponible pour une réception complète.');
                });
            });
        });
    </script>
@endsection