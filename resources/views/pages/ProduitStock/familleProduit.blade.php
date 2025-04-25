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
              <h4 class="card-title">Listes des Famille de Produit</h4>
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
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBoardModal"><i class="fa-solid fa-plus me-1"></i> Ajouter une Famille de Produit</button>
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
                  <th class="text-center">Code Famille</th>
                  <th class="text-center">Libellé</th>
                  <th class="text-center">TVA</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>

              @php
                $i = 1
              @endphp
              <tbody>
                @foreach ($allFamilleProduits as $allFamilleProduit)
                <tr class="text-center">
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
                  <td >{{ $allFamilleProduit->codeFamille }}</td>
                  <td >{{ $allFamilleProduit->libelle }}</td>
                  <td >{{ $allFamilleProduit->TVA }}</td>
                  <td >
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModifyBoardModal{{$allFamilleProduit->idFamPro}}"> Modifier</button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBoardModal{{$allFamilleProduit->idFamPro}}"> Supprimer</button>
                  </td>
                </tr>
                <div class="modal fade" id="ModifyBoardModal{{ $allFamilleProduit->idFamPro }}" tabindex="-1" aria-labelledby="ModifyBoardModal{{ $allFamilleProduit->idFamPro }}" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Modifier une Famille Produit</h1>
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
                      
                      
                    <form action="{{ url('modifFamilleProduit/'.$allFamilleProduit->idFamPro) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-2">
                              <label for="codefamille">Code Famille</label>
                                <input type="text" class="form-control @error('codeFamille') is-invalid @enderror" id="codefamille" name="codeFamille" value="{{ $allFamilleProduit->codeFamille }}">
                                @error('codeFamille')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                    
                            <div class="mb-2">
                              <label for="libelle">Libelle</label>
                                <input type="text" class="form-control @error('libelle') is-invalid @enderror" id="libelle" name="libelle" value="{{ $allFamilleProduit->libelle }}">
                                @error('libelle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                    
                            <div class="mb-2">
                              <label for="TVA">TVA</label>
                                <input type="number" class="form-control @error('TVA') is-invalid @enderror" id="TVA" name="TVA" value="{{ $allFamilleProduit->TVA }}">
                                @error('TVA')
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
                <div class="modal fade" id="deleteBoardModal{{$allFamilleProduit->idFamPro}}" tabindex="-1" aria-labelledby="deleteBoardModal{{$allFamilleProduit->idFamPro}}" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation de suppression</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        Êtes-vous sûr de vouloir supprimer cette famille de produit ?
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>

                        <form action="{{ url('suppFamilleProduit/'.$allFamilleProduit->idFamPro)}}" method="POST">
                          @csrf
                          @method('DELETE')
                          <input type="hidden" name="idFamPro" value="{{$allFamilleProduit->idFamPro}}">
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
                <h1 class="modal-title fs-5" id="exampleModalLabel">Ajouter une Famille de Produit</h1>
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
              
            <form action="{{url('/ajouterFamilleProduit')}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-2">
                        <label for="codeFamille">Code Famille</label>
                        <input type="text" class="form-control @error('codeFamille') is-invalid @enderror" id="codeFamille" name="codeFamille" value="{{ old('codeFamille') }}">
                        @error('codeFamille')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
            
                    <div class="mb-2">
                      <label for="libelle">Libelle</label>
                        <input type="text" class="form-control @error('libelle') is-invalid @enderror" id="libelle" name="libelle" value="{{ old('libelle') }}">
                        @error('libelle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
            
                    <div class="mb-2">
                      <label for="TVA">TVA</label>
                        <input type="number" class="form-control @error('TVA') is-invalid @enderror" id="TVA" name="TVA" value="{{ old('TVA') }}">
                        @error('TVA')
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