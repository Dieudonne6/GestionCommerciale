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
  
  <div class="row justify-content-center">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col">
              <h4 class="card-title">Listes des Produits</h4>
            </div><!--end col-->

            @if (Session::has('status'))
            <br>
            <div class="alert alert-success alert-dismissible">
              {{Session::get('status')}}
            </div>
            @endif

            @if (Session::has('erreur'))
            <br>
            <div class="alert alert-danger alert-dismissible">
              {{Session::get('erreur')}}
            </div>
            @endif
            <div class="col-auto">
              <div class="col-auto">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBoardModal"><i class="fa-solid fa-plus me-1"></i> Ajouter un Produit</button>
              </div><!--end col-->
            </div><!--end col-->
          </div><!--end row-->
        </div>
        <div class="card-body pt-0">
          <div class="table-responsive">
            <table class="table mb-0 checkbox-all" id="datatable_1">
              <thead class="table-light">
                <tr>
                  <th class="text-center">No</th>
                  <th class="text-center">Libellé</th>
                  <th class="text-center">Prix</th>
                  <th class="text-center">Quantite en Stock</th>
                  <th class="text-center">Image</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>

              @php
                $i = 1;
              @endphp
              <tbody>
                @foreach ($allProduits as $allProduit)
                <tr>
                  <td class="text-center">
                    <p class="d-inline-block align-middle mb-0">
                      <span class="font-13 fw-medium">{{ $i }}</span>
                    </p>
                  </td>
                  {{-- <td >
                    <p class="d-inline-block align-middle mb-0">
                      <span class="font-13 fw-medium">{{ $allfournisseur->PrenomF }}</span>
                    </p>
                  </td> --}}
                  <td class="text-center">{{ $allProduit->libelle }}</td>
                  <td class="text-center">{{ $allProduit->prix }}</td>
                  <td class="text-center">
                    @php
                    if ($allProduit->stocke) {
                      $totalStocke = $allProduit->stocke->qteStocke;
                      # code...
                    } else {
                      # code...
                      $totalStocke = 0;
                    }
                    @endphp
                    {{ $totalStocke > 0 ? $totalStocke : '0' }}
                  </td>                  
                  <td class="text-center">
                    <img src="data:image/jpeg;base64,{{ base64_encode($allProduit->image) }}" 
                         alt="Image du produit" 
                         style="width: 70px; height:70px; object-fit: cover; object-position: center;">
                  </td>                  
                  <td class="text-center">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModifyBoardModal{{$allProduit->idPro}}"> Modifier</button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBoardModal{{$allProduit->idPro}}"> Supprimer</button>
                  </td>
                </tr>
                @php
                    $i++;
                @endphp
                @endforeach
              </tbody>
            </table>
          </div>
        </div> 
      </div>
    </div>
  </div>
  
  <!-- Modals for each product -->
  @foreach ($allProduits as $allProduit)
  <div class="modal fade" id="ModifyBoardModal{{ $allProduit->idPro }}" tabindex="-1" aria-labelledby="ModifyBoardModal{{ $allProduit->idPro }}" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Modifier un Produit</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ url('modifProduit/'.$allProduit->idPro) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-2">              
                <label for="libelle">Libelle</label>
                <input type="text" class="form-control @error('libelle') is-invalid @enderror" id="libelle" name="libelle" value="{{ $allProduit->libelle }}">
                @error('libelle')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6 mb-2 form-group">
                  <label for="idMag">Magasin</label>
                  <select id="idMag" name="idMag" class="form-control @error('idMag') is-invalid  @enderror">
                    <option value="0" selected>Aucune</option>
                    @foreach ($magasins as $magasin)
                      <option value="{{ $magasin->idMag }}"
                        {{ optional($allProduit->stocke)->idMag == $magasin->idMag ? 'selected' : '' }}>
                        {{ $magasin->libelle }}
                      </option>
                    @endforeach
                  </select>
                  @error('idMag')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
              </div> 
            </div> 
            
            <div class="row mb-2">
                <!-- Sélection de la Catégorie -->
                <div class="col-md-6 form-group">
                  <label for="idCatPro">Catégorie Produit</label>
                  <select id="idCatPro" name="idCatPro" class="form-control @error('idCatPro') is-invalid @enderror">
                    <option value="0" selected>Aucune</option>
                    @foreach ($allCategorieProduits as $allCategorieProduit)
                      <option value="{{ $allCategorieProduit->idCatPro }}" 
                        {{ $allProduit->idCatPro == $allCategorieProduit->idCatPro ? 'selected' : '' }}>
                        {{ $allCategorieProduit->libelle }}
                      </option>
                    @endforeach
                  </select>
                  @error('idCatPro')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Sélection de la Famille -->
                <div class="col-md-6 form-group">
                  <label for="idFamPro">Famille Produit</label>
                  <select id="idFamPro" name="idFamPro" class="form-control @error('idFamPro') is-invalid @enderror">
                    <option value="0" selected>Aucune</option>
                    @foreach ($allFamilleProduits as $allFamilleProduit)
                      <option value="{{ $allFamilleProduit->idFamPro }}" 
                        {{ $allProduit->idFamPro == $allFamilleProduit->idFamPro ? 'selected' : '' }}>
                        {{ $allFamilleProduit->libelle }}
                      </option>
                    @endforeach
                  </select>
                  @error('idFamPro')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
            </div>  

            <div class="row  mb-2">
              <div class="col-md-6">
                <label for="stockAlert">Seuil d'Alert</label>
                <input type="number" class="form-control @error('stockAlert') is-invalid @enderror" id="stockAlert" name="stockAlert" value="{{ $allProduit->stockAlert ?? 0 }}">
                @error('stockAlert')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6">
                <label for="stockMinimum">Stock Minimum</label>
                <input type="number" class="form-control @error('stockMinimum') is-invalid @enderror" id="stockMinimum" name="stockMinimum" value="{{ $allProduit->stockMinimum ?? 0 }}">
                @error('stockMinimum')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            {{-- <div class="mb-2">
              <label for="qteStocke">Quantité en Stock</label>
              <input type="number" class="form-control @error('qteStocke') is-invalid @enderror" id="qteStocke" name="qteStocke" value="{{ optional($allProduit->stocke)->qteStocke ?? 0 }}">
              @error('qteStocke')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div> --}}

            {{-- <div class="row mb-2">
              <div class="col-md-12">
                <label class="form-label">Mode de fixation du prix</label>


                <div class="form-check">
                  <input class="form-check-input" type="radio" name="price_mode" id="priceManualModify" value="manualModify" checked>
                  <label class="form-check-label" for="priceManualModify">
                    Fixer manuellement le prix de vente
                  </label>
                </div>

                <div class="form-check">
                  <input class="form-check-input" type="radio" name="price_mode" id="priceAutoModify" value="autoModify">
                  <label class="form-check-label" for="priceAutoModify">
                    Prix de vente dynamique en fonction du prix d'achat et de la marge
                  </label>
                </div>

              </div>
            </div> --}}

            {{-- <div class="row mb-2" id="autoPriceFieldsModify">
              <div class="col-md-6 form-group">
                <label for="prixAchatModify">Prix d'achat théorique</label>
                <input type="number" step="0.01" class="form-control" id="prixAchatModify" name="prixAchat" value="{{ $allProduit->prixAchatTheorique }}">
              </div>

              <div class="col-md-6 form-group">
                <label for="margeModify">Marge (%)</label>
                <input type="number" step="0.01" class="form-control" id="margeModify" name="marge" value="{{ $allProduit->marge }}">
              </div>
            </div> --}}
            
            <div class="mb-2">
              <label for="prix">Prix</label>
              <input type="number" class="form-control @error('prix') is-invalid @enderror" id="prixAddModify" name="prix" value="{{ $allProduit->prix }}">
              @error('prix')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-2">
              <label for="desc">Description</label>
              <textarea class="form-control @error('desc') is-invalid @enderror" id="desc" name="desc" rows="4">{{ $allProduit->desc }}</textarea>
              @error('desc')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-2">
              <label for="image">Image (Laisser vide pour conserver l'actuelle)</label>
              <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
              @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-2 text-center">
              <img src="data:image/jpeg;base64,{{ base64_encode($allProduit->image) }}" alt="Ancienne image" style="max-width: 100%; height: auto; border: 1px solid #ddd; padding: 5px; border-radius: 5px;">
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary">Modifier</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="deleteBoardModal{{$allProduit->idPro}}" tabindex="-1" aria-labelledby="deleteBoardModal{{$allProduit->idPro}}" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation de suppression</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Êtes-vous sûr de vouloir supprimer ce produit ?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <form action="{{ url('suppProduit/'.$allProduit->idPro)}}" method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" name="idPro" value="{{$allProduit->idPro}}">
            <input type="submit" class="btn btn-danger" value="Confirmer">
          </form>  
        </div>
      </div>
    </div>
  </div>
  @endforeach
  
  <!-- Add Product Modal -->
  <div class="modal fade" id="addBoardModal" tabindex="-1" aria-labelledby="addBoardModal" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Ajouter un Produit</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{url('/ajouterProduit')}}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-2">
                <label for="libelleAdd">Libelle</label>
                <input type="text" class="form-control @error('libelle') is-invalid @enderror" id="libelleAdd" name="libelle" value="{{ old('libelle') }}">
                @error('libelle')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6 mb-2">
                <label for="qteStockeAdd">Quantité</label>
                <input type="number" name="qteStocke" class="form-control @error('qteStocke') is-invalid @enderror" id="qteStockeAdd" value="0" readonly>
                @error('qteStocke')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 form-group">
                <label for="idCatProAdd">Catégorie Produit</label>
                <select id="idCatProAdd" name="idCatPro" class="form-control @error('idCatPro') is-invalid  @enderror">
                  <option value="" >Aucune</option>
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

              <div class="col-md-6 form-group">
                <label for="idFamProAdd">Famille Produit</label>
                <select id="idFamProAdd" name="idFamPro" class="form-control @error('idFamPro') is-invalid  @enderror">
                  <option value="" >Aucune</option>
                  @foreach ($allFamilleProduits as $allFamilleProduit)
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
            <br>
            <div class="row">
              <div class="col-md-6 mb-2">
                <label for="stockAlert">Seuil d'Alert</label>
                <input type="number" class="form-control @error('stockAlert') is-invalid @enderror" id="stockAlert" name="stockAlert" value="{{ old('stockAlert') }}">
                @error('stockAlert')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6 mb-2">
                <label for="stockMinimum">Stock Minimum</label>
                <input type="number" class="form-control @error('stockMinimum') is-invalid @enderror" id="stockMinimum" name="stockMinimum" value="{{ old('stockMinimum') }}">
                @error('stockMinimum')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div> 
            <div class="row mb-2">
              <div class="col-md-12 form-group">
                <label for="idMagAdd">Magasin</label>
                <select id="idMagAdd" name="idMag" class="form-control @error('idMag') is-invalid  @enderror" required>
                  <option value="0">Aucune</option>
                  @foreach ($magasins as $magasin)
                    <option value="{{ $magasin->idMag }}">
                      {{ $magasin->libelle }}
                    </option>
                  @endforeach
                </select>
                @error('idMag')
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
                <input type="number" step="0.01" class="form-control" id="marge" value="40" name="marge">
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

            {{-- </div> --}}
            <div class="row mb-2">
              <div class="col-md-12 form-group">
                <label for="imageAdd">Image</label>
                <input type="file" class="form-control @error('image') is-invalid @enderror" id="imageAdd" name="image" accept="image/*" onchange="previewImage(event)" required>
                @error('image')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="row mb-2">
              <div class="col-md-12">
                <label for="descAdd">Description</label>
                <textarea class="form-control @error('desc') is-invalid @enderror" id="descAdd" name="desc" rows="4">{{ old('desc') }}</textarea>
                @error('desc')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <!-- Zone de prévisualisation -->
            <div class="mb-2 text-center">
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
  
  @endsection

<script>


// pour la modification
  document.addEventListener('DOMContentLoaded', function () {

    const priceAutoModify = document.getElementById('priceAutoModify');
    const priceManualModify = document.getElementById('priceManualModify');
    const autoFieldsModify = document.getElementById('autoPriceFieldsModify');

    const prixVenteModify = document.getElementById('prixAddModify');
    const prixAchatModify = document.getElementById('prixAchatModify');
    const margeModify = document.getElementById('margeModify');

    // état initial
    autoFieldsModify.style.display = 'none';
    prixVenteModify.removeAttribute('readonly');

    priceAutoModify.addEventListener('change', function () {
      if (this.checked) {
        autoFieldsModify.style.display = 'flex';
        prixVenteModify.setAttribute('readonly', true);
        calculerPrixVenteModify();
      }
    });

    priceManualModify.addEventListener('change', function () {
      if (this.checked) {
        autoFieldsModify.style.display = 'none';
        prixVenteModify.removeAttribute('readonly');
        prixVenteModify.value = '';
      }
    });

    function calculerPrixVenteModify() {
      const achatModify = parseFloat(prixAchatModify.value) || 0;
      const tauxModify = parseFloat(margeModify.value) || 0;

      const prixModify = achatModify + (achatModify * tauxModify / 100);
      prixVenteModify.value = prixModify.toFixed(2);
    }

    prixAchatModify.addEventListener('input', calculerPrixVenteModify);
    margeModify.addEventListener('input', calculerPrixVenteModify);

  });



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

      const prix = achat + (achat * taux / 100);
      prixVente.value = prix.toFixed(2);
    }

    prixAchat.addEventListener('input', calculerPrixVente);
    marge.addEventListener('input', calculerPrixVente);

  });
</script>




<script>
  document.addEventListener('DOMContentLoaded', function () {
      @if (session()->has('errorModalId'))
          var modalId = "{{ session('errorModalId') }}";
          var modalElement = document.getElementById(modalId);
          if (modalElement) {
              var myModal = new bootstrap.Modal(modalElement);
              myModal.show();
          }
      @endif
  });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
      // Cible tous les modals
      var modals = document.querySelectorAll('.modal');
  
      modals.forEach(function(modal) {
        // Écouteur pour la fermeture du modal (via le bouton "Fermer")
        modal.addEventListener('hidden.bs.modal', function () {
          resetModalErrors(modal);  // Réinitialise les erreurs
        });
  
        // Écoute du clic sur le bouton "Annuler"
        var cancelButton = modal.querySelector('.btn-secondary');
        if (cancelButton) {
          cancelButton.addEventListener('click', function () {
            resetModalErrors(modal);  // Réinitialise les erreurs
          });
        }
      });
  
      // Fonction pour réinitialiser les erreurs dans le modal
      function resetModalErrors(modal) {
        // Réinitialiser le contenu des messages d'erreur
        var errorElements = modal.querySelectorAll('.invalid-feedback');
        errorElements.forEach(function(errorElement) {
          errorElement.textContent = '';  // Supprimer le texte des erreurs
        });
  
        // Réinitialiser les champs de saisie
        var inputFields = modal.querySelectorAll('.form-control');
        inputFields.forEach(function(inputField) {
          inputField.classList.remove('is-invalid');  // Enlever la classe d'erreur
        });
        
        // Effacer les erreurs dans la session si besoin (cela évite que les erreurs persistent)
        @if(Session::has('error'))
          @php
            session()->forget('error');
          @endphp
        @endif
      }
    });
</script>

<script>
  function previewImage(event) {
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
  



