@extends('layouts.master')
@section('content')

<div class="container">
    <h2>Exporter une table en Excel</h2>
    <form action="{{ route('export') }}" method="GET">
        @csrf
        <div class="form-group">
            <label for="database">Sélectionnez la base de donné</label>
            <select name="database" id="database" class="form-control">
                <option value="" selected>Aucune</option>
                @foreach ($databases as $database)
                <option value="{{ $database }}">
                    {{ $database }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="table">Sélectionnez la table à exporter</label>
            <select name="table" id="table" class="form-control">
            <!-- Les tables seront chargées dynamiquement en fonction de la base de données sélectionnée -->
            </select>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Exporter</button>
    </form>
</div>
@endsection


<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('database').addEventListener('change', function() {
            const databaseName = this.value;
            
            if (databaseName) {
                fetch(`{{ url('/get-tables') }}/${databaseName}`)
                    .then(response => response.json())
                    .then(tables => {
                        const tableSelect = document.getElementById('table');
                        tableSelect.innerHTML = '';

                        tables.forEach(table => {
                            const option = document.createElement('option');
                            option.value = table;
                            option.textContent = table;
                            tableSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                    });
            } else {
                const tableSelect = document.getElementById('table');
                tableSelect.innerHTML = ''; // Réinitialiser les options
            }
        });
    });

</script>