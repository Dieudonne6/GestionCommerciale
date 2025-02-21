@extends('layouts.master')
@section('content')

<div class="container">
    <h2>Exporter une table en Excel</h2>
    <form action="{{ route('export') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="table">Sélectionnez la base de donné</label>
            <select name="table" id="table" class="form-control">
                <option value="" selected>Aucune</option>
                @foreach ($dbNames as $dbName)
                <option value="{{ $dbName }}">
                    {{ $dbName }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="table">Sélectionnez la table à exporter</label>
            <select name="table" id="table" class="form-control">
                <option value="" selected>Aucune</option>
                @foreach ($tables as $table)
                <option value="{{ $table }}">
                    {{ $table }}
                </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Exporter</button>
    </form>
</div>
@endsection
