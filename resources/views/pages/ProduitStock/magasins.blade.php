@extends('layouts.master')
@section('content')
<style>
  .modal-header {
    background-color: #fff !important;
  }
  .modal-title {
    color: #000 !important;
  }
</style>
<div class="container-xxl">
    <div class="row">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible">
                {{ session('success') }}
            </div>
        @endif
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Liste des Magasins</h4>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMagasinModal">
                                <i class="fa-solid fa-plus me-1"></i> Ajouter un Magasin
                            </button>
                        </div>
                    </div>
                </div>
                @if(Session::has('status'))
                <div id="statusAlert" class="alert alert-success">
                  {{ Session::get('status') }}
                </div>
                @endif
                @if(Session::has('erreur'))
                <div id="statusAlert" class="alert alert-danger">
                  {{ Session::get('erreur') }}
                </div>
                @endif
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table mb-0" id="datatable_1">
                            <thead class="table-light">
                                <tr >
                                    <th class="text-center">Code Magasin</th>
                                    <th class="text-center">Libellé</th>
                                    <th class="text-center">Adresse</th>
                                    <th class="text-center">Entreprise</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($magasins as $magasin)
                                    <tr class="text-center">
                                        <td>{{ $magasin->codeMagasin }}</td>
                                        <td>{{ $magasin->libelle }}</td>
                                        <td>{{ $magasin->Adresse }}</td>
                                        <td>{{ $magasin->entreprise->nom }}</td>
                                        <td>
                                            <!-- Bouton Détails -->
                                            <button type="button" class="btn btn-info " data-bs-toggle="modal" data-bs-target="#detailsModal{{ $magasin->idMag }}">
                                                Détails
                                            </button>
                                         {{--    <a href=""
                                                    class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#editModal{{ $magasin->idMag }}"
                                                    style="margin-right: 24px; margin-left: 43px;">
                                                    Modifier
                                            </a> --}}
                                            <!-- Bouton Modifier -->
                                            <a href="{{ route('magasins.addProduct', $magasin->idMag) }}"
                                                    class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#editModal{{ $magasin->idMag }}"
                                                    style="margin-right: 24px; margin-left: 43px;">
                                                    Ajouter Produit
                                            </a>
                                            
                                            <!-- Bouton Supprimer -->
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="##deleteModal{{ $magasin->idMag }}">
                                                 Supprimer
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal Ajouter Produit -->
                                    <div class="modal fade" id="editModal{{ $magasin->idMag }}" tabindex="-1" aria-labelledby="editModalLabel{{ $magasin->idMag }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel{{ $magasin->idMag }}">Ajouter un Produit</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('magasins.addProduct', $magasin->idMag) }}" enctype="multipart/form-data" method="POST">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-md-6 mb-2">
                                                                <label for="libelle">Produit</label>
                                                                <input type="text" name="libelle" class="form-control" required>
                                                            </div>
                                                            <div class="col-md-6 mb-2">
                                                                <label for="desc">Description</label>
                                                                <input type="text" name="desc" class="form-control" required>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6 mb-2">
                                                                <label for="qteStocke">Quantité</label>
                                                                <input type="number" name="qteStocke" class="form-control" required>
                                                            </div>
                                                            <div class="col-md-6 mb-2">
                                                                <label for="prix">Prix</label>
                                                                <input type="number" name="prix" class="form-control" required>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6 mb-2">
                                                                <label for="stockAlert">Seuil d'Alert</label>
                                                                <input type="text" name="stockAlert" class="form-control" required>
                                                            </div>                        
                                                            <div class="col-md-6 mb-2">
                                                                <label for="stockMinimum">Seuil Minimum</label>
                                                                <input type="text" name="stockMinimum" class="form-control" required>
                                                            </div>                                        
                                                        </div>
                                
                                                        <div class="row">
                                                            <div class="col-md-6 mb-2">
                                                            <label for="idCatPro">Catégorie du Produit</label>
                                                            <select name="idCatPro" class="form-control @error('idCatPro') is-invalid  @enderror">
                                                                <option value="0" {{-- {{ $produit->categorieP == 0 ? 'selected' : '' }} --}}>Aucune</option>
                                                                @foreach ($allCategorieProduits as $allCategorieProduit)
                                                                    <option value="{{ $allCategorieProduit->idCatPro }}">
                                                                        {{ $allCategorieProduit->libelle }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                                @error('idCatPro')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            <div class="col-md-6 mb-2">
                                                            <label for="idFamPro">Famillle du Produit</label>
                                                            <select name="idFamPro" class="form-control @error('idFamPro') is-invalid  @enderror">
                                                                <option value="0" {{-- {{ $produit->categorieP == 0 ? 'selected' : '' }} --}}>Aucune</option>
                                                                @foreach ($allFamilleProduits as $allFamilleProduit)
                                                                    {{-- <option value="{{ $allFamilleProduit->idFamPro }}" {{ old('idFamPro', $allCategorieProduit->idFamPro) == $allCategorieProduit->idCatPro ? 'selected' : '' }}> --}}
                                                                    <option value="{{ $allFamilleProduit->idFamPro }}">
                                                                        {{ $allFamilleProduit->libelle }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                                @error('idFamPro')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <label for="image">Image</label>
                                                                <input type="file" name="image" class="form-control" onchange="previewimage(event)" accept="image/*" required>
                                                            </div>

                                                            <div class="col-md-6 mb-2 text-center">
                                                                <img id="imagePreview" src="#" alt="Prévisualisation" style="display: none; max-width: 100%; height: auto; border: 1px solid #ddd; padding: 5px; border-radius: 5px;">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <button type="submit" class="btn btn-primary">Ajouter</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Supprimer -->
                                    <div class="modal fade" id="deleteModal{{ $magasin->idMag }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $magasin->idMag }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $magasin->idMag }}">Supprimer le Magasin</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Êtes-vous sûr de vouloir supprimer ce magasin ?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('magasins.destroy', $magasin->idMag) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

@foreach($magasins as $magasin)
                <div class="modal fade" id="detailsModal{{ $magasin->idMag }}" tabindex="-1" aria-labelledby="detailsModalLabel{{ $magasin->idMag }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="detailsModalLabel{{ $magasin->idMag }}">Détails du Magasin</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <h6>Produits associés :</h6>
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th>Produit</th>
                                                                    <th>Image</th>
                                                                    <th>Catégorie</th>
                                                                    <th>Quantité</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse ($magasin->stocke as $stock)
                                                                    <tr>
                                                                        <td>{{ $stock->produit->libelle ?? 'Non défini' }}</td>
                                                                        <td><img src="data:image/jpeg;base64,{{ base64_encode($stock->produit->image) }}" alt="Image du produit" style="width: 100px; height: auto;"></td>
                                                                        <td>{{ $stock->produit->categorieProduit->libelle ?? 'Non défini' }}</td>
                                                                        <td>{{ $stock->qteStocke }}</td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="3" class="text-center">Aucun produit disponible</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
@endforeach
                                    <!-- Modal d'ajout -->
                                    <div class="modal fade" id="addMagasinModal" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">Ajouter un Magasin</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            {{--@if ($errors->any())
                                            <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                            </div>
                                            @endif --}}
                                            <form action="{{ route('magasins.ajouterMagasin') }}" method="POST">
                                                @csrf
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="codeMagasin">Code </label>
                                                    <input type="text" class="form-control @error('codeMagasin') is-invalid  @enderror" name="codeMagasin" value="{{ old('codeMagasin') }}" required>
                                                    @error('codeMagasin')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="libelle">libelle</label>
                                                    <input type="text" class="form-control @error('libelle') is-invalid  @enderror" name="libelle" value="{{ old('libelle') }}" required>
                                                    @error('libelle')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="Adresse">Adresse</label>
                                                    <input type="text" class="form-control @error('Adresse') is-invalid  @enderror" name="Adresse" value="{{ old('Adresse') }}" required>
                                                    @error('Adresse')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="entreprise">Entreprise</label>
                                                    <select name="idE" class="form-control @error('idE') is-invalid  @enderror">
                                                        <option value="">Aucune</option>
                                                        @foreach ($entreprises as $entreprise)
                                                            <option value="{{ $entreprise->idE }}" {{ old('idE') == $entreprise->idE ? 'selected' : '' }}>
                                                                {{ $entreprise->nom }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                        @error('idE')
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
                                    </div>
                                </div>
                            </div>
    </div>
</div>
@endsection

<!-- Script pour afficher le modal après actualisation si erreurs -->
  @if (session('showAddMagasinModal'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var myModal = new bootstrap.Modal(document.getElementById('addMagasinModal'));
            myModal.show();
        });
    </script>
  @endif

    @if (session('showModifyMagasinModal'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var magasinId = "{{ session('showModifyMagasinModal') }}";
            var myModal = new bootstrap.Modal(document.getElementById('editModal' + magasinId));
            myModal.show();
        });
    </script>
  @endif

@section('styles')
    <style>
        #datatable_1 td,
        #datatable_1 th {
            text-align: center;
        }

        /* Centrer les images dans les cellules */
        #datatable_1 td img {
            display: block;
            margin: 0 auto;
        }

        /* Centrer le texte dans les liens aussi */
        #datatable_1 td a {
            display: inline-block;
            text-align: center;
        }

        .modal-content {
            border-radius: 8px;
        }
    </style>
@endsection
<script>
  function previewimage(event) {
      var input = event.target;
      var preview = document.getElementById('imagePreview');
  
      if (input.files && input.files[0]) {
          var reader = new FileReader();
          
          reader.onload = function(e) {
              preview.src = e.target.result;
              preview.style.display = 'block';
          };
          
          reader.readAsDataURL(input.files[0]); // Lecture du fichier comme URL
      } else {
          preview.style.display = 'none';
      }
  }
</script>