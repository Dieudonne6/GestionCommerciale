@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="card mt-4">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Gestion des Entreprises</h4>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addEntrepriseModal">
                            <i class="fa-solid fa-plus me-1"></i> Ajouter une entreprise
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success" id="successMessage">
                        {{ session('success') }}
                    </div>
                @endif

                <h5>Liste des Entreprises</h5>
                <table class="table-responsive table mb-0" id="datatable_1">
                    <thead class="table-light">
                        <tr>
                            <th>Logo</th>
                            <th>Nom</th>
                            <th>IFU</th>
                            <th>Adresse</th>
                            <th>Mail</th>
                            <th>Entreprise Parent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($entreprises as $entreprise)
                            <tr>
                                <td>
                                    @if ($entreprise->logo)
                                        <img src="{{ asset('storage/' . $entreprise->logo) }}" alt="Logo"
                                            width="50">
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $entreprise->nom }}</td>
                                <td>{{ $entreprise->IFU }}</td>
                                <td>{{ $entreprise->adresse }}</td>
                                <td>{{ $entreprise->mail }}</td>
                                <td>{{ $entreprise->parent ? $entreprise->parent->nom : '-' }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editEntrepriseModal{{ $entreprise->idE }}">Modifier</button>
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#deleteEntrepriseModal{{ $entreprise->idE }}">Supprimer</button>
                                </td>
                            </tr>

                            <!-- Modal de modification -->
                            <div class="modal fade" id="editEntrepriseModal{{ $entreprise->idE }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('entreprise.update', $entreprise->idE) }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Modifier l'entreprise</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <!-- Colonne de gauche -->
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Nom</label>
                                                            <input type="text" class="form-control" name="nom"
                                                                value="{{ $entreprise->nom }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">IFU</label>
                                                            <input type="text" class="form-control" name="IFU"
                                                                value="{{ $entreprise->IFU }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Logo</label>
                                                            @if ($entreprise->logo)
                                                                <div class="mb-2">
                                                                    <img src="{{ asset('storage/' . $entreprise->logo) }}"
                                                                        alt="Logo" width="100">
                                                                </div>
                                                            @endif
                                                            <input type="file" class="form-control" name="logo">
                                                        </div>
                                                    </div>
                                                    <!-- Colonne de droite -->
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Adresse</label>
                                                                    <input type="text" class="form-control"
                                                                        name="adresse" value="{{ $entreprise->adresse }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Téléphone</label>
                                                                    <input type="text" class="form-control"
                                                                        name="telephone"
                                                                        value="{{ $entreprise->telephone }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Mail</label>
                                                                    <input type="email" class="form-control"
                                                                        name="mail" value="{{ $entreprise->mail }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label">RCCM</label>
                                                                    <input type="text" class="form-control"
                                                                        name="RCCM" value="{{ $entreprise->RCCM }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Régime</label>
                                                                    <input type="text" class="form-control"
                                                                        name="regime" value="{{ $entreprise->regime }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Entreprise Parent
                                                                        (ID)
                                                                    </label>
                                                                    <input type="number" class="form-control"
                                                                        name="idParent"
                                                                        value="{{ $entreprise->idParent }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Modal de suppression -->
                            <div class="modal fade" id="deleteEntrepriseModal{{ $entreprise->idE }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('entreprise.destroy', $entreprise->idE) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Supprimer l'entreprise</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Êtes-vous sûr de vouloir supprimer
                                                    <strong>{{ $entreprise->nom }}</strong> ?
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-danger">Supprimer</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="addEntrepriseModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('entreprise.storeEntreprise') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <!-- En-tête avec un fond coloré -->
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Ajouter une entreprise</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <!-- Corps de la modal -->
                        <div class="modal-body">
                            <div class="container-fluid">
                                <!-- Informations de base -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nom <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nom" required placeholder="Nom de l'entreprise" autofocus autocomplete="off">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">IFU <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="IFU" required placeholder="Numéro IFU" pattern="[0-9]{13}" maxlength="13">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="logoInput">Logo</label>
                                        <input type="file" class="form-control" name="logo" id="logoInput" accept="image/*" onchange="previewLogo(event)">
                                    </div>
                                    <div class="col-md-6 mb-3 text-center">
                                        <img id="logoPreview" src="#" alt="Aperçu du logo" style="display: none; max-width: 200px; margin: auto;" />
                                    </div>
                                </div>
                                <!-- Coordonnées -->
                                <div class="mb-3">
                                    <label class="form-label">Adresse</label>
                                    <input type="text" class="form-control" name="adresse" placeholder="Adresse complète">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Téléphone</label>
                                        <input type="text" class="form-control" name="telephone" placeholder="Numéro de téléphone" pattern="[0-9]{10}" maxlength="10">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mail</label>
                                        <input type="email" class="form-control" name="mail" placeholder="Adresse email">
                                    </div>
                                </div>
                                <!-- Informations administratives -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="regime">Régime fiscal</label>
                                        <select class="form-control" id="regime" name="regime">
                                            <option value="">Sélectionnez un Régime fiscal</option>
                                            <option value="TPS">TPS</option>
                                            <option value="TPS/reel">TPS/Réel</option>
                                            <option value="reel">Réel</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Régime de commerce (RCCM)</label>
                                        <input type="text" class="form-control" name="RCCM" placeholder="Numéro RCCM">
                                    </div>
                                </div>
                                <!-- Entreprise principale -->
                                <div class="mb-3">
                                    <label class="form-label" for="idParent">Entreprise Principale</label>
                                    <select class="form-control" id="idParent" name="idParent">
                                        <option value="">Aucune</option>
                                        @foreach ($entreprises as $entreprise)
                                            <option value="{{ $entreprise->id }}">{{ $entreprise->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- Pied de page avec les boutons d'action -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Ajouter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Script pour l'aperçu du logo -->
        <script>
            function previewLogo(event) {
                var input = event.target;
                var img = document.getElementById('logoPreview');
                var file = input.files[0];
        
                if (file) {
                    var fileSize = file.size / 1024 / 1024; // Convertir en Mo
                    var validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        
                    if (!validTypes.includes(file.type)) {
                        alert("Veuillez sélectionner une image au format JPG, PNG ou GIF.");
                        input.value = "";
                        img.style.display = 'none';
                        return;
                    }
        
                    if (fileSize > 2) { // 2 Mo max
                        alert("L'image ne doit pas dépasser 2 Mo.");
                        input.value = "";
                        img.style.display = 'none';
                        return;
                    }
        
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        img.src = e.target.result;
                        img.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            }
        </script>

    </div>
@endsection
