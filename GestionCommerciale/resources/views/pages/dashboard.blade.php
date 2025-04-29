@extends('layouts.master')

@section('content')
    <div class="container">
        <h1 class="mt-4">Tableau de Bord</h1>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Statistiques des Réceptions</h5>
                    </div>
                    <div class="card-body">
                        <p>Nombre total de réceptions : {{ $totalReceptions }}</p>
                        <p>Réceptions complètes : {{ $receptionsCompletes }}</p>
                        <p>Réceptions en cours : {{ $receptionsEnCours }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Statistiques des Commandes</h5>
                    </div>
                    <div class="card-body">
                        <p>Nombre total de commandes : {{ $totalCommandes }}</p>
                        <p>Commandes validées : {{ $commandesValidees }}</p>
                        <p>Commandes en attente : {{ $commandesEnAttente }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Dernières Réceptions</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Référence BL</th>
                                    <th>Date de Réception</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dernieresReceptions as $reception)
                                    <tr>
                                        <td>{{ $reception->reference }}</td>
                                        <td>{{ \Carbon\Carbon::parse($reception->date)->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $reception->statutRecep == 'complète' ? 'success' : 'warning' }}">
                                                {{ $reception->statutRecep }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection