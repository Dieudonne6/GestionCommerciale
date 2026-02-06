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
              <h4 class="card-title">Listes des Entreprises</h4>
            </div><!--end col-->

            {{-- <a href="{{ url('/export-entreprises') }}" class="btn btn-success">
              Exporter en Excel
            </a> --}}

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
              @if (!$Entreprises)
              @else
              <div class="col-auto">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBoardModal"><i class="fa-solid fa-plus me-1"></i> Ajouter une Entreprise</button>
              </div><!--end col--> 
                  
              @endif

            </div><!--end col-->
          </div><!--end row-->
        </div>
        <div class="card-body pt-0">
          <div class="table-responsive">
            <table class="table mb-0 checkbox-all" id="datatable_1">
              <thead class="table-light">
                <tr>
                  <th >No</th>
                  {{-- <th >Logo</th> --}}
                  <th >Nom</th>
                  <th >IFU</th>
                  <th >Adresse</th>
                  <th >Email</th>
                  <th >Entreprise Prin</th>
                  <th >Actions</th>
                </tr>
              </thead>

              @php
                $i = 1
              @endphp
              <tbody>
                @foreach ($Entreprises as $allEntreprise)
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
                  {{-- <td>
                    <img src="data:logo/jpeg;base64,{{ base64_encode($allEntreprise->logo) }}" alt="logo de l'entreprise" style="width: 100px; height: auto;">
                  </td>                   --}}
                  <td >{{ $allEntreprise->nom }}</td>
                  <td >{{ $allEntreprise->IFU }}</td>
                  <td >{{ $allEntreprise->adresse }}</td>
                  <td >{{ $allEntreprise->mail }}</td>
                  <td>
                    {{ optional($Entreprises->firstWhere('idE', $allEntreprise->idParent))->nom ?? 'Aucune' }}
                </td>                  
                {{-- <td>
                    @php
                        $totalStocke = $allEntreprise->stocke->sum('qteStocke');
                    @endphp
                    {{ $totalStocke > 0 ? $totalStocke : 'Aucun stock' }}
                </td>                   --}}

                <td >
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModifyBoardModal{{$allEntreprise->idE}}"> Modifier</button>
                    {{-- <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBoardModal{{$allEntreprise->idE}}"> Supprimer</button> --}}
                  </td>
                </tr>


                <div class="modal fade" id="ModifyBoardModal{{ $allEntreprise->idE }}" tabindex="-1" aria-labelledby="ModifyBoardModal{{ $allEntreprise->idE }}" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Modifier une Entreprise</h1>
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
                      
                      
                    <form action="{{ url('modifierEntreprise/'.$allEntreprise->idE) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                <label for="nom">Nom</label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ $allEntreprise->nom }}">
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                </div>
                        
                                <div class="col-md-6 mb-2">
                                    <label for="IFU">IFU</label>
                                    <input type="text" class="form-control @error('IFU') is-invalid @enderror" id="IFU" name="IFU" value="{{ $allEntreprise->IFU }}">
                                    @error('IFU')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label for="telephone">Telephone</label>
                                    <input type="text" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ $allEntreprise->telephone }}">
                                    @error('telephone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            
                        
                                <div class="col-md-6 mb-2">
                                    <label for="mail">E-mail</label>
                                    <input type="text" class="form-control @error('mail') is-invalid @enderror" id="mail" name="mail" value="{{ $allEntreprise->mail }}">
                                    @error('mail')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label for="adresse">Adresse</label>
                                    <input type="text" class="form-control @error('adresse') is-invalid @enderror" id="adresse" name="adresse" value="{{ $allEntreprise->adresse }}">
                                    @error('adresse')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                        
                                <div class="col-md-6 mb-2">
                                    <label for="RCCM">RCCM</label>
                                    <input type="text" class="form-control @error('RCCM') is-invalid @enderror" id="RCCM" name="RCCM" value="{{ $allEntreprise->RCCM }}">
                                    @error('RCCM')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label for="regime">Régime</label>
                                    <select id="regime" name="regime" class="form-control @error('regime') is-invalid @enderror">
                                        <option value="">Sélectionner un régime</option>
                                        <option value="TPS" {{ $allEntreprise->regime == 'TPS' ? 'selected' : '' }}>TPS</option>
                                        <option value="TVA" {{ $allEntreprise->regime == 'TVA' ? 'selected' : '' }}>TVA</option>
                                    </select>
                                    @error('regime')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                        
                            <div class="col-md-6 mb-2"">
                                <label for="idParent">Entreprise Principale</label>
                                <select id="idParent" name="idParent" class="form-control @error('idParent') is-invalid @enderror">
                                    <option value="">Aucune</option>
                                    @foreach ($Entreprises as $allEntrpri)
                                        <option value="{{ $allEntrpri->idE }}" 
                                            {{ $allEntreprise->idParent == $allEntrpri->idE ? 'selected' : '' }}>
                                            {{ $allEntrpri->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('idParent')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                          <div class="mb-2">
                            <label for="token">Token</label>
                            <textarea class="form-control" name="token" id="token" rows="5">{{ $allEntreprise->token }}</textarea>
                            @error('token')
                              <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                          </div>
                        </div>

                        
                            <div class="row">
                                <div class=" mb-2">
                                    <label for="logo">Logo</label>
                                    <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo">
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                        
                                <div class="col-md-6 mb-2 text-center">
                                    <img src="data:logo/jpeg;base64,{{ base64_encode($allEntreprise->logo) }}" alt="Ancien logo" style="max-width: 100%; height: auto; border: 1px solid #ddd; padding: 5px; border-radius: 5px;">
                                </div>
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
                <div class="modal fade" id="deleteBoardModal{{$allEntreprise->idE}}" tabindex="-1" aria-labelledby="deleteBoardModal{{$allEntreprise->idE}}" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation de suppression</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        Êtes-vous sûr de vouloir supprimer cette Entreprise ?
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>

                        <form action="{{ url('suppEntreprise/'.$allEntreprise->idE)}}" method="POST">
                          @csrf
                          @method('DELETE')
                          <input type="hidden" name="idE" value="{{$allEntreprise->idE}}">
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
                <h1 class="modal-title fs-5" id="exampleModalLabel">Ajouter une Entreprise</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              
            <form action="{{url('/ajouterEntreprise')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="nom">Nom</label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}">
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                
                        <div class="col-md-6 mb-2">
                            <label for="IFU">IFU</label>
                            <input type="text" class="form-control @error('IFU') is-invalid @enderror" id="IFU" name="IFU" value="{{ old('IFU') }}">
                            @error('IFU')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="telephone">Téléphone</label>
                            <input type="text" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone') }}">
                            @error('telephone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                
                        <div class="col-md-6 mb-2">
                            <label for="mail">E-mail</label>
                            <input type="text" class="form-control @error('mail') is-invalid @enderror" id="mail" name="mail" value="{{ old('mail') }}">
                            @error('mail')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="adresse">Adresse</label>
                            <input type="text" class="form-control @error('adresse') is-invalid @enderror" id="adresse" name="adresse" value="{{ old('adresse') }}">
                            @error('adresse')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                
                        <div class="col-md-6 mb-2">
                            <label for="RCCM">RCCM</label>
                            <input type="text" class="form-control @error('RCCM') is-invalid @enderror" id="RCCM" name="RCCM" value="{{ old('RCCM') }}">
                            @error('RCCM')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="regime">Régime</label>
                            <select id="regime" name="regime" class="form-control @error('regime') is-invalid @enderror">
                                <option value="" selected>Sélectionner un régime</option>
                                <option value="TPS" {{ old('regime') == 'TPS' ? 'selected' : '' }}>TPS</option>
                                <option value="TVA" {{ old('regime') == 'TVA' ? 'selected' : '' }}>TVA</option>
                            </select>
                            @error('regime')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                
                        <div class="col-md-6 mb-2">
                            <label for="idParent">Entreprise Principale</label>
                            <select id="idParent" name="idParent" class="form-control">
                                <option value="" selected>Aucune</option>
                                @foreach ($Entreprises as $allEntrpri)
                                <option value="{{ $allEntrpri->idE }}">
                                    {{ $allEntrpri->nom }}
                                </option>
                                @endforeach
                            </select>
                            {{-- @error('idParent')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror --}}
                        </div>
                    </div>

                        <div class="row">
                          <div class="mb-2">
                            <label for="token">Token</label>
                            <textarea class="form-control" name="token" id="token" rows="5"></textarea>
                            @error('token')
                              <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                          </div>
                        </div>
                
                    <div class="row">
                        <div class="col-md-12">
                            <label for="logoAdd">Logo</label>
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logoAdd" name="logo" accept="image/*" onchange="previewlogo(event)">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                
                        <div class="col-md-6 mb-2 text-center">
                            <img id="logoPreview" src="#" alt="Prévisualisation" style="display: none; max-width: 100%; height: auto; border: 1px solid #ddd; padding: 5px; border-radius: 5px;">
                        </div>
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

<script>
  function previewlogo(event) {
      var input = event.target;
      var preview = document.getElementById('logoPreview');
  
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
  
