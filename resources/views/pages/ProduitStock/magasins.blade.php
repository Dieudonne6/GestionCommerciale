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
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $magasin->idMag }}">
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
                                                                <label for="libelle">Libelle</label>
                                                                <input type="text" name="libelle" id="libelle" class="form-control @error('libelle') is-invalid @enderror" value="{{ old('libelle') }}">
                                                                @error('libelle')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            <div class="col-md-6 mb-2">
                                                                <label for="qteStocke">Quantité</label>
                                                                <input type="number" name="qteStocke" class="form-control" value="0" readonly>
                                                                @error('qteStocke')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6 mb-2">
                                                            <label for="idCatPro">Catégorie du Produit</label>
                                                            <select name="idCatPro" id="idCatPro" class="form-control @error('idCatPro') is-invalid  @enderror">
                                                                <option value="" {{-- {{ $produit->categorieP == 0 ? 'selected' : '' }} --}}>Aucune</option>
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
                                                            <label for="idFamProAdd">Famillle du Produit</label>
                                                            <select name="idFamPro" id="idFamProAdd" class="form-control @error('idFamPro') is-invalid  @enderror">
                                                                <option value="" data-coef="">Aucune</option>
                                                                @foreach ($allFamilleProduits as $allFamilleProduit)
                                                                    {{-- <option value="{{ $allFamilleProduit->idFamPro }}" {{ old('idFamPro', $allCategorieProduit->idFamPro) == $allCategorieProduit->idCatPro ? 'selected' : '' }}> --}}
                                                                    <option 
                                                                    value="{{ $allFamilleProduit->idFamPro }}"
                                                                    data-coef="{{ $allFamilleProduit->coeff }}"
                                                                    >
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
                                                            <div class="col-md-6 mb-2">
                                                                <label for="stockAlert">Seuil d'Alert</label>
                                                                <input type="text" name="stockAlert" id="stockAlert" class="form-control @error('stockAlert') is-invalid @enderror" value="{{ old('stockAlert') }}">
                                                                @error('stockAlert')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>                        
                                                            <div class="col-md-6 mb-2">
                                                                <label for="stockMinimum">Seuil Minimum</label>
                                                                <input type="text" name="stockMinimum" class="form-control @error('stockMinimum') is-invalid @enderror" id="stockMinimum" value="{{ old('stockMinimum') }}">
                                                                @error('stockMinimum')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>                                        
                                                        </div>

                                                        <div class="row mb-2">
                                                            <div class="col-md-12">
                                                            <label class="form-label">Mode de fixation du prix</label>


                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="price_mode" id="priceManual" value="manual" checked>
                                                                <label class="form-check-label" for="priceManual">
                                                                Fixer manuellement le prix de vente
                                                                </label>
                                                            </div>

                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="price_mode" id="priceAuto" value="auto">
                                                                <label class="form-check-label" for="priceAuto">
                                                                Prix de vente dynamique en fonction du prix d'achat et de la marge
                                                                </label>
                                                            </div>

                                                            </div>
                                                        </div>

                                                        <div class="row mb-2" id="autoPriceFields">
                                                        <div class="col-md-6 form-group">
                                                            <label for="prixAchat">Prix d'achat théorique</label>
                                                            <input type="number" step="0.01" class="form-control" id="prixAchat" name="prixAchat">
                                                        </div>

                                                        <div class="col-md-6 form-group">
                                                            <label for="marge">Marge (%)</label>
                                                            <input type="number" step="0.01" class="form-control" id="marge"  name="marge">
                                                        </div>
                                                        </div>


                                                        <div class="row mb-2">
                                                        <div class="col-md-12 form-group">
                                                            <label for="prixAdd">Prix Vente</label>
                                                            <input type="text" class="form-control @error('prix') is-invalid @enderror" id="prixAdd" name="prix" value="{{ old('prix') }}">
                                                            {{-- <input type="number"
                                                            class="form-control @error('prix') is-invalid @enderror"
                                                            id="prixAdd"
                                                            name="prix"
                                                            value="{{ old('prix') }}"
                                                            readonly> --}}

                                                            @error('prix')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        </div>

                                                        <input type="hidden"  class="form-control @error('prixReelAchat') is-invalid @enderror" id="prixReelAchat" name="prixReelAchat" value="0">

                                                        <div class="row mb-2">
                                                            <div class="col-md-12">
                                                                <label for="descAdd">Description</label>
                                                                <textarea class="form-control @error('desc') is-invalid @enderror" id="descAdd" name="desc" rows="4">{{ old('desc') }}</textarea>
                                                                @error('desc')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <label for="image">Image</label>
                                                                <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" class="form-control" onchange="previewimage(event)" accept="image/*">
                                                                @error('image')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
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
                                                    Le magasin avec tout les produits associés seront supprimés.
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
                                                    {{-- <select name="idE" class="form-control @error('idE') is-invalid  @enderror">
                                                        <option value="">Aucune</option>
                                                        @foreach ($entreprises as $entreprise)
                                                            <option value="{{ $entreprise->idE }}" {{ old('idE') == $entreprise->idE ? 'selected' : '' }}>
                                                                {{ $entreprise->nom }}
                                                            </option>
                                                        @endforeach
                                                    </select> --}}

                                                    <select disabled name="idE" class="form-control @error('idE') is-invalid @enderror">
                                                        <option value="">Aucune</option>

                                                        @foreach ($entreprises as $entreprise)
                                                            <option value="{{ $entreprise->idE }}"
                                                                {{ (old('idE', $entrepriseId) == $entreprise->idE) ? 'selected' : '' }}>
                                                                {{ $entreprise->nom }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    {{-- Champ caché pour envoyer la valeur --}}
                                                    <input type="hidden" name="idE" value="{{ old('idE', $entrepriseId) }}">
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


{{-- Pour calculer le prix de vente --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const familleSelect = document.getElementById('idFamProAdd');
        const margeInput = document.getElementById('marge');
        const priceAutoRadio = document.getElementById('priceAuto');
        const prixAchat = document.getElementById('prixAchat');
        const prixVente = document.getElementById('prixAdd');

        function recalculerPrix() {
            if (!priceAutoRadio.checked) return;

            const achat = parseFloat(prixAchat.value) || 0;
            const taux = parseFloat(margeInput.value) || 0;

            const prix = Math.ceil(achat + (achat * taux / 100));
            prixVente.value = prix;
        }

        familleSelect.addEventListener('change', function () {

            const selectedOption = this.options[this.selectedIndex];
            const coef = selectedOption.dataset.coef;

            // reset marge
            margeInput.value = 0;

            if (coef !== undefined && coef !== '') {
                margeInput.value = coef;
            }

            // 🔥 recalcul automatique du prix
            recalculerPrix();
        });

    });
</script>


<script>
      // pour l'ajout

    document.addEventListener('DOMContentLoaded', function () {

        const priceAuto = document.getElementById('priceAuto');
        const priceManual = document.getElementById('priceManual');
        const autoFields = document.getElementById('autoPriceFields');

        const prixVente = document.getElementById('prixAdd');
        const prixAchat = document.getElementById('prixAchat');
        const marge = document.getElementById('marge');

        // état initial
        autoFields.style.display = 'none';
        // prixVente.removeAttribute('readonly');

        priceAuto.addEventListener('change', function () {
        if (this.checked) {
            autoFields.style.display = 'flex';
            prixVente.setAttribute('readonly', true);
            calculerPrixVente();
        }
        });

        priceManual.addEventListener('change', function () {
        if (this.checked) {
            autoFields.style.display = 'none';
            prixVente.removeAttribute('readonly');
            prixVente.value = '';
        }
        });

        function calculerPrixVente() {
        const achat = parseFloat(prixAchat.value) || 0;
        const taux = parseFloat(marge.value) || 0;

        const prix =  Math.ceil(achat + (achat * taux / 100));
        prixVente.value = prix;
        }

        prixAchat.addEventListener('input', calculerPrixVente);
        marge.addEventListener('input', calculerPrixVente);

    });
</script>

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