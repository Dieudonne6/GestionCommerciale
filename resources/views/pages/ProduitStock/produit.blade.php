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
                $i = 1
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
                        $totalStocke = $allProduit->stocke->qteStocke;
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


                <div class="modal fade" id="ModifyBoardModal{{ $allProduit->idPro }}" tabindex="-1" aria-labelledby="ModifyBoardModal{{ $allProduit->idPro }}" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Modifier un Produit</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                      </div>
                      {{-- @if($errors->any())
                      <div class="alert alert-danger alert-dismissible">
                          <ul>
                              @foreach($errors->all() as $error)
                                  <li>{{ $error }}</li>
                              @endforeach
                          </ul>
                      </div>
                      @endif --}}
                      <?php $error = Session::get('error');?>
            
                      {{-- @if(Session::has('error'))
                      <div class="alert alert-danger alert-dismissible">
                        {{ Session::get('error')}}
                      </div>
                      @endif --}}
                      
                      
                    <form action="{{ url('modifProduit/'.$allProduit->idPro) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-2">
                                <label for="libelle">Libelle</label>
                                <input type="text" class="form-control @error('libelle') is-invalid @enderror" id="libelle" name="libelle" value="{{ $allProduit->libelle }}">
                                @error('libelle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Sélection de la Catégorie -->
                            <div class="form-group">
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
                            <div class="form-group">
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

                            <div class="form-group">
                              <label for="idMag">Magasin</label>
                              <select id="idMag" name="idMag" class="form-control @error('idMag') is-invalid  @enderror">
                                  <option value="0" selected>Aucune</option>
                                  @foreach ($magasins as $magasin)
                                      {{-- <option value="{{ $allFamilleProduit->idFamPro }}" {{ old('idFamPro', $allCategorieProduit->idFamPro) == $allCategorieProduit->idCatPro ? 'selected' : '' }}> --}}
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

                    
                            <div class="mb-2">
                                <label for="prix">Prix</label>
                                <input type="number" class="form-control @error('prix') is-invalid @enderror" id="prix" name="prix" value="{{ $allProduit->prix }}">
                                @error('prix')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- <div class="mb-2">
                              <label for="qteStocke">Quantité</label>
                              <input type="number" name="qteStocke" class="form-control @error('qteStocke') is-invalid @enderror" id="qteStocke" name="qteStocke" value="{{ optional($allProduit->stocke->first())->qteStocke }}">
                              @error('qteStocke')
                                  <div class="invalid-feedback">{{ $message }}</div>
                              @enderror
                            </div> --}}

                            <div class="mb-2">
                              <label for="desc">Description</label>
                              <textarea class="form-control @error('desc') is-invalid @enderror" id="desc" name="desc" rows="4">{{ $allProduit->desc }}</textarea>
                              @error('desc')
                                  <div class="invalid-feedback">{{ $message }}</div>
                              @enderror
                            </div>
                    
                            <div class="mb-2">
                                <label for="image">Image</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" value="{{ $allProduit->image }}">
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
                @php
                    $i++;
                @endphp
                @endforeach
              </tbody>
            </table>
          </div>
        </div> 
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
                      <label for="prixAdd">Prix</label>
                      <input type="number" class="form-control @error('prix') is-invalid @enderror" id="prixAdd" name="prix" value="{{ old('prix') }}">
                      @error('prix')
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
                <br>
                  <div class="row">
                    <div class="col-md-6 mb-2">
                        <label for="stockAlert">Seuil d'Alert</label>
                        <input type="text" class="form-control @error('stockAlert') is-invalid @enderror" id="stockAlert" name="stockAlert" value="{{ old('stockAlert') }}">
                        @error('stockAlert')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <label for="stockMinimum">Stock Minimum</label>
                        <input type="text" class="form-control @error('stockMinimum') is-invalid @enderror" id="stockMinimum" name="stockMinimum" value="{{ old('stockMinimum') }}">
                        @error('stockMinimum')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                  </div> 
                <div class="row">
                  <div class="col-md-6 form-group">
                    <label for="idMag">Magasin</label>
                    <select id="idMag" name="idMag" class="form-control @error('idMag') is-invalid  @enderror">
                        <option value="0" selected>Aucune</option>
                        @foreach ($magasins as $magasin)
                            {{-- <option value="{{ $allFamilleProduit->idFamPro }}" {{ old('idFamPro', $allCategorieProduit->idFamPro) == $allCategorieProduit->idCatPro ? 'selected' : '' }}> --}}
                            <option value="{{ $magasin->idMag }}">
                                {{ $magasin->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('idMag')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>                  

                  <div class="col-md-6 mb-2">
                    <label for="qteStocke">Quantité</label>
                    <input type="number" readonly name="qteStocke" class="form-control @error('qteStocke') is-invalid @enderror" id="qteStocke" name="qteStocke" value="0" required>
                    @error('qteStocke')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  
                </div>
{{--                 <div class="col-md-6 mb-2">
                  <label for="qteStocke">Quantité</label>
                  <input type="number" name="qteStocke" class="form-control @error('qteStocke') is-invalid @enderror" id="qteStocke" name="qteStocke">
                </div> --}}
                <div class="col-md-12">
                  <label for="imageAdd">Image</label>
                  <input type="file" class="form-control @error('image') is-invalid @enderror" id="imageAdd" name="image" accept="image/*" onchange="previewImage(event)" required>
                  @error('image')
                      <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-12">
                  <label for="descAdd">Description</label>
                  <textarea class="form-control @error('desc') is-invalid @enderror" id="desc" name="desc" rows="4">{{ old('desc') }}</textarea>
                  @error('desc')
                      <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
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
      </div>
    </div>
  </div>
  
  @endsection

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
  