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
              <h4 class="card-title">Listes des Modes de Paiement</h4>
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
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBoardModal"><i class="fa-solid fa-plus me-1"></i> Ajouter un Mode de Paiement</button>
              </div><!--end col-->
            </div><!--end col-->
          </div><!--end row-->
        </div>
        <div class="card-body pt-0">
          <div class="table-responsive">
            <table class="table mb-0 checkbox-all" id="datatable_1">
              <thead class="table-light">
                <tr>
                  <th>No</th>
                  <th>Libellé</th>
                  <th>Actions</th>
                </tr>
              </thead>

              @php
                $i = 1
              @endphp
              <tbody>
                @foreach ($modePaiements as $modePaiement)
                <tr>
                  <td>
                    <p class="d-inline-block align-middle mb-0">
                      <span class="font-13 fw-medium">{{ $i }}</span>
                    </p>
                  </td>
                  <td>{{ $modePaiement->libelle }}</td>

                <td>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModifyBoardModal{{$modePaiement->idModPaie}}"> Modifier</button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBoardModal{{$modePaiement->idModPaie}}"> Supprimer</button>
                  </td>
                </tr>

                <div class="modal fade" id="ModifyBoardModal{{ $modePaiement->idModPaie }}" tabindex="-1" aria-labelledby="ModifyBoardModal{{ $modePaiement->idModPaie }}" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Modifier un Mode de Paiement</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      
                    <form action="{{ url('modepaiement/'.$modePaiement->idModPaie) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                <label for="libelle">Libellé</label>
                                <input type="text" class="form-control @error('libelle') is-invalid @enderror" id="libelle" name="libelle" value="{{ $modePaiement->libelle }}">
                                @error('libelle')
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
                <div class="modal fade" id="deleteBoardModal{{$modePaiement->idModPaie}}" tabindex="-1" aria-labelledby="deleteBoardModal{{$modePaiement->idModPaie}}" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation de suppression</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        Êtes-vous sûr de vouloir supprimer ce mode de paiement ?
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>

                        <form action="{{ url('modepaiement/'.$modePaiement->idModPaie)}}" method="POST">
                          @csrf
                          @method('DELETE')
                          <input type="hidden" name="idModPaie" value="{{$modePaiement->idModPaie}}">
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
                <h1 class="modal-title fs-5" id="exampleModalLabel">Ajouter un Mode de Paiement</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

            <form action="{{url('/modepaiement')}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label for="libelle">Libellé</label>
                            <input type="text" class="form-control @error('libelle') is-invalid @enderror" id="libelle" name="libelle" value="{{ old('libelle') }}">
                            @error('libelle')
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
            var modalId = "{{ session('errorModalId') }}";
            if (modalId) {
                var modalElement = document.getElementById(modalId);
                if (modalElement) {
                    var myModal = new bootstrap.Modal(modalElement);
                    myModal.show();
                }
            }
        @endif

        @if (Session::has('showAddModal'))
            var addModal = document.getElementById('addBoardModal');
            if (addModal) {
                var myModal = new bootstrap.Modal(addModal);
                myModal.show();
            }
        @endif

        @if (Session::has('showModifyModal'))
            var modifyModalId = "ModifyBoardModal{{ session('showModifyModal') }}";
            var modifyModal = document.getElementById(modifyModalId);
            if (modifyModal) {
                var myModal = new bootstrap.Modal(modifyModal);
                myModal.show();
            }
        @endif
    });
  </script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
      var modals = document.querySelectorAll('.modal');
  
      modals.forEach(function(modal) {
        modal.addEventListener('hidden.bs.modal', function () {
          resetModalErrors(modal);
        });
  
        var cancelButton = modal.querySelector('.btn-secondary');
        if (cancelButton) {
          cancelButton.addEventListener('click', function () {
            resetModalErrors(modal);
          });
        }
      });
  
      function resetModalErrors(modal) {
        var errorElements = modal.querySelectorAll('.invalid-feedback');
        errorElements.forEach(function(errorElement) {
          errorElement.textContent = '';
        });
  
        var inputFields = modal.querySelectorAll('.form-control');
        inputFields.forEach(function(inputField) {
          inputField.classList.remove('is-invalid');
        });
        
        @if(Session::has('error'))
          @php
            session()->forget('error');
          @endphp
        @endif
      }
    });
</script>
