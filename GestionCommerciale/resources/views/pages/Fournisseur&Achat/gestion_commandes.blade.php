@extends('layouts.master')

@section('content')
    <div class="container">
        <h1 class="mt-4">Gestion des Commandes d'Achat</h1>

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
                            <h4 class="card-title">Liste des Commandes d'Achat</h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('commandes.create') }}" class="btn btn-primary">
                                <i class="fa-solid fa-plus me-1"></i> Ajouter une commande
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
                                    <th>Référence</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($commandes as $commande)
                                    <tr>
                                        <td>{{ $commande->numCmd }}</td>
                                        <td>{{ \Carbon\Carbon::parse($commande->date)->format('d/m/Y') }}</td>
                                        <td>{{ $commande->reference }}</td>
                                        <td>
                                            <span class="badge bg-{{ $commande->statutCom == 'validée' ? 'success' : 'warning' }}">
                                                {{ $commande->statutCom }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <a href="{{ route('commandes.edit', $commande->idCommande) }}"
                                                    class="btn btn-warning me-2 {{ $commande->statutCom == 'complète' ? 'disabled' : '' }}">
                                                    <i class="fas fa-edit me-1"></i> Modifier
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('commandes.destroy', $commande->idCommande) }}"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-danger {{ $commande->statutCom == 'complète' ? 'disabled' : '' }}"
                                                        onclick="return confirm('Voulez-vous vraiment supprimer cette commande ?')">
                                                        <i class="fas fa-trash me-1"></i> Supprimer
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Aucune commande enregistrée.</td>
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
        });
    </script>
@endsection