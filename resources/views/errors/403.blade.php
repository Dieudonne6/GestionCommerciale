@extends('layouts.master')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="text-center">

        <h1 class="display-1 text-danger">403</h1>

        <h3 class="mb-3">Accès refusé</h3>

        <p class="text-muted mb-4">
            Vous n’avez pas les droits nécessaires pour accéder à cette page.
            <br>
            Si vous pensez qu’il s’agit d’une erreur, contactez l’administrateur.
        </p>

        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary me-2">
            ⬅ Retour
        </a>

        <a href="{{ url('/tableaudebord') }}" class="btn btn-primary">
             Tableau de bord
        </a>

    </div>
</div>
@endsection
