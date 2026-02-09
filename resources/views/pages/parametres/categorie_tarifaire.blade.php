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
              <h4 class="card-title">Listes des Categories Tarifaires</h4>
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
              <div class="col-auto">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBoardModal"><i class="fa-solid fa-plus me-1"></i> Ajouter une categorie Tarif</button>
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
                  {{-- <th >Logo</th> --}}
                  <th >Code</th>
                  <th >Libelle</th>
                  <th >Statut</th>
                  <th >Type de reduc</th>
                  <th >Valeur de reduc</th>
                  <th >Actions</th>
                </tr>
              </thead>

              @php
                $i = 1
              @endphp
              <tbody>
                @foreach ($categorietarifaires as $categorietarifaire)
                <tr>
                  <td >
                    <p class="d-inline-block align-middle mb-0">
                      <span class="font-13 fw-medium">{{ $i }}</span>
                    </p>
                  </td>

                  <td >{{ $categorietarifaire->code }}</td>
                  <td >{{ $categorietarifaire->libelle }}</td>
                  <td>
                        @if($categorietarifaire->actif)
                            <span class="badge bg-success">Actif</span>
                        @else
                            <span class="badge bg-danger">Desactivé</span>
                        @endif
                  </td>
                  <td >{{ $categorietarifaire->type_reduction }}</td>
                  <td >{{ $categorietarifaire->valeur_reduction }}</td>


                <td >
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModifyBoardModal{{$categorietarifaire->id}}"> Modifier</button>
                    {{-- <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBoardModal{{$categorietarifaire->id}}"> Desactiver</button> --}}
                    <button type="button"
                        class="btn {{ $categorietarifaire->actif ? 'btn-danger' : 'btn-secondary' }}"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteBoardModal{{$categorietarifaire->id}}">
                        
                        {{ $categorietarifaire->actif ? 'Désactiver' : 'Activer' }}
                    </button>
                  </td>
                </tr>


                <div class="modal fade" id="ModifyBoardModal{{ $categorietarifaire->id }}" tabindex="-1" aria-labelledby="ModifyBoardModal{{ $categorietarifaire->id }}" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Modifier une Categorie Tarifaire</h1>
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
                      
                      
                    <form action="{{ url('categorie_tarifaire_edit/'.$categorietarifaire->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                <label for="code">Code</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ $categorietarifaire->code }}">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                </div>
                        
                                <div class="col-md-6 mb-2">
                                    <label for="libelle">Libellé</label>
                                    <input type="text" class="form-control @error('libelle') is-invalid @enderror" id="libelle" name="libelle" value="{{ $categorietarifaire->libelle }}">
                                    @error('libelle')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label for="type_reduction">Type de réduction</label>
                                    <select id="type_reduction" name="type_reduction" class="form-control @error('type_reduction') is-invalid @enderror">
                                        <option value="">Sélectionner un type</option>
                                        <option value="pourcentage" {{ $categorietarifaire->type_reduction == 'pourcentage' ? 'selected' : '' }}>Pourcentage</option>
                                        <option value="fixe" {{ $categorietarifaire->type_reduction == 'fixe' ? 'selected' : '' }}>Montant fixe</option>
                                    </select>
                                    @error('type_reduction')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            
                        
                                {{-- <div class="col-md-6 mb-2">
                                    <label for="valeur_reduction">Valeur de la réduction</label>
                                    <input type="number" step="0.01" class="form-control @error('valeur_reduction') is-invalid @enderror" id="valeur_reduction" name="valeur_reduction" value="{{ $categorietarifaire->valeur_reduction }}">
                                    @error('valeur_reduction')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div> --}}

                                <div class="col-md-6 mb-2">
                                    <label for="valeur_reduction">Valeur de la réduction</label>

                                    <input
                                        type="number"
                                        step="0.01"
                                        class="form-control"
                                        id="valeur_reduction"
                                        name="valeur_reduction"
                                        value="{{ $categorietarifaire->valeur_reduction }}"
                                    >

                                    <div class="invalid-feedback">
                                        La valeur du pourcentage ne peut pas dépasser 100 %
                                    </div>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="aib">AIB</label>
                                    <select id="aib" name="aib" class="form-control @error('aib') is-invalid @enderror">
                                        <option value="">collecter ou pas</option>
                                        <option value="1" {{ $categorietarifaire->aib == '1' ? 'selected' : '' }}>OUI</option>
                                        <option value="0" {{ $categorietarifaire->aib == '0' ? 'selected' : '' }}>NON</option>
                                    </select>
                                    @error('aib')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                <div class="modal fade" id="deleteBoardModal{{$categorietarifaire->id}}" tabindex="-1" aria-labelledby="deleteBoardModal{{$categorietarifaire->id}}" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation de suppression</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        Êtes-vous sûr de vouloir
                        <strong>
                            {{ $categorietarifaire->actif ? 'désactiver' : 'activer' }}
                        </strong>
                        cette catégorie tarifaire ?                      
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>

                        <form action="{{ url('categorie_tarifaire_activate/'.$categorietarifaire->id)}}" method="POST">
                          @csrf
                          {{-- @method('DELETE') --}}
                          <input type="hidden" name="id" value="{{$categorietarifaire->id}}">
                          {{-- <input type="submit" class="btn btn-danger" value="Confirmer"> --}}
                          <input type="submit"
                            class="btn {{ $categorietarifaire->actif ? 'btn-danger' : 'btn-primary' }}"
                            value="{{ $categorietarifaire->actif ? 'Désactiver' : 'Activer' }}">
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
                <h1 class="modal-title fs-5" id="exampleModalLabel">Ajouter une Catégorie</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              
            <form action="{{url('/categorie_tarifaire_create')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                <label for="code">Code</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                </div>
                        
                                <div class="col-md-6 mb-2">
                                    <label for="libelle">Libellé</label>
                                    <input type="text" class="form-control @error('libelle') is-invalid @enderror" id="libelle" name="libelle">
                                    @error('libelle')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label for="type_reduction">Type de réduction</label>
                                    <select id="type_reduction" name="type_reduction" class="form-control @error('type_reduction') is-invalid @enderror">
                                        <option value="">Sélectionner un type</option>
                                        <option value="pourcentage">Pourcentage</option>
                                        <option value="fixe">Montant fixe</option>
                                    </select>
                                    @error('type_reduction')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            
                        
                                <div class="col-md-6 mb-2">
                                    <label for="valeur_reduction">Valeur de la réduction</label>
                                    <input type="number" step="0.01" class="form-control @error('valeur_reduction') is-invalid @enderror" id="valeur_reduction" name="valeur_reduction" >
                                    @error('valeur_reduction')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="aib">AIB</label>
                                    <select id="aib" name="aib" class="form-control">
                                        <option value="">Collecter ou pas</option>
                                        <option value="1">OUI</option>
                                        <option value="0">NON</option>
                                    </select>
                                    @error('aib')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
document.addEventListener('DOMContentLoaded', function () {

    function handleReductionValidation(modal) {
        const typeSelect = modal.querySelector('select[name="type_reduction"]');
        const valueInput = modal.querySelector('input[name="valeur_reduction"]');

        if (!typeSelect || !valueInput) return;

        function applyRules() {
            if (typeSelect.value === 'pourcentage') {
                valueInput.setAttribute('max', '100');
                valueInput.setAttribute('min', '1');

                if (parseFloat(valueInput.value) > 100) {
                    valueInput.value = 100;
                    valueInput.classList.add('is-invalid');
                }
            } else {
                valueInput.removeAttribute('max');
                valueInput.removeAttribute('min');
                valueInput.classList.remove('is-invalid');
            }
        }

        // Quand on change le type
        typeSelect.addEventListener('change', applyRules);

        // Quand on tape la valeur
        // valueInput.addEventListener('input', function () {
        //     if (typeSelect.value === 'pourcentage' && parseFloat(this.value) > 100) {
        //         this.value = 100;
        //         this.classList.add('is-invalid');
        //     } else {
        //         this.classList.remove('is-invalid');
        //     }
        // });


        valueInput.addEventListener('input', function () {
            if (typeSelect.value === 'pourcentage' && parseFloat(this.value) >= 100) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });

        // Initialisation (important pour le modal Modifier)
        applyRules();
    }

    // Appliquer à tous les modals
    document.querySelectorAll('.modal').forEach(function (modal) {
        modal.addEventListener('shown.bs.modal', function () {
            handleReductionValidation(modal);
        });
    });

});
</script>



  
