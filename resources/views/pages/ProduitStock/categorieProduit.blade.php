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
              <h4 class="card-title">Listes des categories de Produit</h4>
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
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBoardModal"><i class="fa-solid fa-plus me-1"></i> Ajouter une Categorie de Produit</button>
              </div><!--end col-->
            </div><!--end col-->
          </div><!--end row-->
        </div>
        <div class="card-body pt-0">
          <div class="table-responsive">
            <table class="table mb-0 checkbox-all" id="datatable_1">
              <thead class="table-light">
                <tr>
                  <th >No</th>
                  <th >Libellé</th>
                  <th >Code Categorie</th>
                  <th >Actions</th>
                </tr>
              </thead>

              @php
                  $i = 1
              @endphp
              <tbody>
                @foreach ($allCategorieProduits as $allCategorieProduit)
                <tr>
                  <td >
                    <p class="d-inline-block align-middle mb-0">
                      <span class="font-13 fw-medium">{{ $i }}</span>
                    </p>
                  </td>
                  {{-- <td >
                    <p class="d-inline-block align-middle mb-0">
                      <span class="font-13 fw-medium">{{ $allfournisseur->PrenomF }}</span>
                    </p>
                  </td> --}}
                  <td >{{ $allCategorieProduit->libelle }}</td>
                  <td >{{ $allCategorieProduit->codeCatPro }}</td>
                  <td >
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModifyBoardModal{{$allCategorieProduit->idCatPro}}"> Modifier</button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBoardModal{{$allCategorieProduit->idCatPro}}"> Supprimer</button>
                  </td>
                </tr>
                <div class="modal fade" id="ModifyBoardModal{{ $allCategorieProduit->idCatPro }}" tabindex="-1" aria-labelledby="ModifyBoardModal{{ $allCategorieProduit->idCatPro }}" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Modifier une Categorie de Produit</h1>
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
                      
                      
                      <form action="{{url('modifCategorieProduit/'.$allCategorieProduit->idCatPro)}}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">

                          <div class="mb-2">
                            <input type="text" class="form-control @error('codeCatPro') is-invalid @enderror"  placeholder="code Categorie" name="codeCatPro" value="{{ $allCategorieProduit->codeCatPro }}">
                            @error('codeCatPro')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                          </div>
                          {{-- <div class="mb-2">
                            <input type="text" class="form-control"  placeholder="Prenom" name="PrenomF" value="{{ $allCategorieProduit->PrenomF }}">
                          </div> --}}
                          <div class="mb-2">
                            <input type="text" class="form-control @error('libelle') is-invalid @enderror"  placeholder="libelle" name="libelle" value="{{ $allCategorieProduit->libelle }}">
                            @error('libelle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                <div class="modal fade" id="deleteBoardModal{{$allCategorieProduit->idCatPro}}" tabindex="-1" aria-labelledby="deleteBoardModal{{$allCategorieProduit->idCatPro}}" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation de suppression</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        Êtes-vous sûr de vouloir supprimer cette categorie de produit ?
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>

                        <form action="{{ url('suppCategorieProduit/'.$allCategorieProduit->idCatPro)}}" method="POST">
                          @csrf
                          @method('DELETE')
                          <input type="hidden" name="idCatPro" value="{{$allCategorieProduit->idCatPro}}">
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
                <h1 class="modal-title fs-5" id="exampleModalLabel">Ajouter une Categorie de Produit</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              {{-- @if ($errors->any())
                  <div class="alert alert-danger alert-dismissible">
                      <ul>
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
              @endif --}}
              
              <form action="{{url('/ajouterCategorieProduit')}}" method="POST">
                @csrf
                <div class="modal-body">
                  <div class="mb-2">
                    <input type="text" class="form-control @error('codeCatPro') is-invalid @enderror"  placeholder="code Categorie" name="codeCatPro" value="{{old('codeCatPro')}}">
                    @error('codeCatPro')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  {{-- <div class="mb-2">
                    <input type="text" class="form-control"  placeholder="Prenom" name="PrenomF">
                  </div> --}}
                  <div class="mb-2">
                    <input type="text" class="form-control @error('libelle') is-invalid @enderror"  placeholder="libelle" name="libelle" value="{{old('libelle')}}">
                    @error('libelle')
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
  
  @endsection

  <script>
    document.addEventListener('DOMContentLoaded', function () {
        @if ($errors->any())
            var modalId = "{{ session('errorModalId') }}"; // Récupérez l'ID du modal ayant les erreurs
            if (modalId) {
                var modalElement = document.getElementById(modalId);
                if (modalElement) {
                    var myModal = new bootstrap.Modal(modalElement);
                    myModal.show();
                }
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
  