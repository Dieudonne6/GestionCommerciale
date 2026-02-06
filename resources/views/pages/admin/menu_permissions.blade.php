@extends('layouts.master')
@section('content')

<style>

    html, body {
        overflow-x: hidden;
    }

    .permissions-table-wrapper {
        max-height: 70vh;
        overflow: auto;
        position: relative;
    }

    /* Header fixe */
    .permissions-table thead th {
        position: sticky;
        top: 0;
        background: #f8f9fa;
        z-index: 3;
    }

    /* Colonne Menu fixe */
    .permissions-table .sticky-col {
        position: sticky;
        left: 0;
        background: #ffffff;
        z-index: 2;
        min-width: 240px;
    }

    /* Coin haut-gauche */
    .permissions-table .sticky-header.sticky-col {
        z-index: 4;
        background: #e9ecef;
    }

    /* Lisibilité */
    .permissions-table th,
    .permissions-table td {
        white-space: nowrap;
        vertical-align: middle;
    }

</style>

<div class="container">
    <h3>Gestion des permissions par menu / rôle</h3>

    <form id="permissionsForm">
        @csrf

        <!-- 🔽 NOUVEAU : wrapper scroll -->
        <div class="table-responsive permissions-table-wrapper">

            <!-- 🔽 NOUVEAU : classe permissions-table -->
            <table class="table table-bordered permissions-table">
                <thead>
                    <tr>
                        <!-- 🔽 NOUVEAU -->
                        <th class="sticky-col sticky-header">Menu</th>

                        @foreach($roles as $role)
                            <!-- 🔽 NOUVEAU -->
                            <th class="text-center sticky-header">
                                {{ $role->libelle }}
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    @foreach($menus as $menu)
                    <tr>
                        <!-- 🔽 NOUVEAU -->
                        <td class="sticky-col menu-col">
                            <strong>{{ $menu->label }}</strong><br>
                            <small>{{ $menu->code }}</small>
                        </td>

                        @foreach($roles as $role)
                            @php
                                $pivot = $menu->roles->firstWhere('idRole', $role->idRole);
                            @endphp

                            <td style="min-width:220px;">
                                <div class="d-flex justify-content-around">

                                    {{-- View --}}
                                    <input type="hidden" name="permissions[{{ $menu->id }}][{{ $role->idRole }}][view]" value="0">
                                    <label>
                                        <input type="checkbox"
                                            name="permissions[{{ $menu->id }}][{{ $role->idRole }}][view]"
                                            value="1"
                                            @if($pivot && $pivot->pivot->can_view) checked @endif>
                                        Voir
                                    </label>

                                    {{-- Create --}}
                                    <input type="hidden" name="permissions[{{ $menu->id }}][{{ $role->idRole }}][create]" value="0">
                                    <label>
                                        <input type="checkbox"
                                            name="permissions[{{ $menu->id }}][{{ $role->idRole }}][create]"
                                            value="1"
                                            @if($pivot && $pivot->pivot->can_create) checked @endif>
                                        Créer
                                    </label>

                                    {{-- Edit --}}
                                    <input type="hidden" name="permissions[{{ $menu->id }}][{{ $role->idRole }}][edit]" value="0">
                                    <label>
                                        <input type="checkbox"
                                            name="permissions[{{ $menu->id }}][{{ $role->idRole }}][edit]"
                                            value="1"
                                            @if($pivot && $pivot->pivot->can_edit) checked @endif>
                                        Modifier
                                    </label>

                                    {{-- Delete --}}
                                    <input type="hidden" name="permissions[{{ $menu->id }}][{{ $role->idRole }}][delete]" value="0">
                                    <label>
                                        <input type="checkbox"
                                            name="permissions[{{ $menu->id }}][{{ $role->idRole }}][delete]"
                                            value="1"
                                            @if($pivot && $pivot->pivot->can_delete) checked @endif>
                                        Suppr
                                    </label>

                                </div>
                            </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <button type="button" id="savePermsBtn" class="btn btn-primary">
                Sauvegarder
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
    <script>
        document.getElementById('savePermsBtn').addEventListener('click', function() {
            const form = document.getElementById('permissionsForm');
            const fd = new FormData(form);

            // Convert FormData to nested object (simple)
            const obj = {};
            for (let pair of fd.entries()) {
                const name = pair[0]; // permissions[menuId][roleId][view]
                const val = pair[1];
                // create nested structure
                name.replace(/\]/g,'').split('[').slice(1).reduce((acc, key, i, arr) => {
                    if (i === arr.length -1) {
                        acc[key] = val;
                    } else {
                        acc[key] = acc[key] || {};
                    }
                    return acc[key];
                }, obj);
            }

            fetch('{{ route("menupermissions.update") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ permissions: obj })
            }).then(r => r.json()).then(resp => {
                if (resp.success) alert(resp.message);
                else alert('Erreur');
            }).catch(e => { console.error(e); alert('Erreur réseau'); });
        });
    </script>
@endpush











