@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">Définir le délai d’alerte</h5>
                </div>

                <div class="card-body">
                    <form action="{{route('params.store')}}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">
                                Délai avant expiration (en jours)
                            </label>
                            <input type="number"
                                   name="delai_alerte"
                                   class="form-control"
                                   min="0"
                                   max="90"
                                   placeholder="Ex : 7"
                                   required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Enregistrer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
