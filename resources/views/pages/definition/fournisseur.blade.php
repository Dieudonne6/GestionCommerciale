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
              <h4 class="card-title">Listes des Fournisseurs</h4>
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
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBoardModal"><i class="fa-solid fa-plus me-1"></i> Ajouter un Fournisseur</button>
              </div><!--end col-->
            </div><!--end col-->
          </div><!--end row-->
        </div>
        <div class="card-body pt-0">
          <div class="table-responsive">
            <table class="table mb-0 checkbox-all" id="datatable_1">
              <thead class="table-light">
                <tr>
                  <th >identité</th>
                  {{-- <th >Prénoms</th> --}}
                  <th >Adresse</th>
                  <th >Contact</th>
                  <th >Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($allfournisseurs as $allfournisseur)
                <tr>
                  <td >
                    <p class="d-inline-block align-middle mb-0">
                      <span class="font-13 fw-medium">{{ $allfournisseur->identiteF }}</span>
                    </p>
                  </td>
                  {{-- <td >
                    <p class="d-inline-block align-middle mb-0">
                      <span class="font-13 fw-medium">{{ $allfournisseur->PrenomF }}</span>
                    </p>
                  </td> --}}
                  <td >{{ $allfournisseur->AdresseF }}</td>
                  <td >{{ $allfournisseur->ContactF }}</td>
                  <td >
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModifyBoardModal{{$allfournisseur->idF}}"> Modifier</button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBoardModal{{$allfournisseur->idF}}"> Supprimer</button>
                  </td>
                </tr>
                <div class="modal fade" id="ModifyBoardModal{{ $allfournisseur->idF }}" tabindex="-1" aria-labelledby="ModifyBoardModal{{ $allfournisseur->idF }}" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Modifier un Fournisseur</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                      </div>
                      @if($errors->any())
                      <div class="alert alert-danger alert-dismissible">
                          <ul>
                              @foreach($errors->all() as $error)
                                  <li>{{ $error }}</li>
                              @endforeach
                          </ul>
                      </div>
                      @endif
                      <?php $error = Session::get('error');?>
            
                      @if(Session::has('error'))
                      <div class="alert alert-danger alert-dismissible">
                        {{ Session::get('error')}}
                      </div>
                      @endif
                      
                      
                      <form action="{{url('modifFournisseur/'.$allfournisseur->idF)}}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                          <div class="mb-2">
                            <input type="text" class="form-control"  placeholder="identiteF" name="identiteF" value="{{ $allfournisseur->identiteF }}">
                          </div>
                          {{-- <div class="mb-2">
                            <input type="text" class="form-control"  placeholder="Prenom" name="PrenomF" value="{{ $allfournisseur->PrenomF }}">
                          </div> --}}
                          <div class="mb-2">
                            <input type="text" class="form-control"  placeholder="Adresse" name="AdresseF" value="{{ $allfournisseur->AdresseF }}">
                          </div>
                          <div>
                            <input type="text" class="form-control"  placeholder="Contact" name="ContactF" value="{{ $allfournisseur->ContactF }}">
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
                <div class="modal fade" id="deleteBoardModal{{$allfournisseur->idF}}" tabindex="-1" aria-labelledby="deleteBoardModal{{$allfournisseur->idF}}" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation de suppression</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        Êtes-vous sûr de vouloir supprimer ce fournisseur ?
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>

                        <form action="{{ url('suppFournisseur/'.$allfournisseur->idF)}}" method="POST">
                          @csrf
                          @method('DELETE')
                          <input type="hidden" name="idF" value="{{$allfournisseur->idF}}">
                          <input type="submit" class="btn btn-danger" value="Confirmer">
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
        <div class="modal fade" id="addBoardModal" tabindex="-1" aria-labelledby="addBoardModal" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Ajouter un Fournisseur</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              @if ($errors->any())
                  <div class="alert alert-danger alert-dismissible">
                      <ul>
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
              @endif
              
              <form action="{{url('/ajouterFournisseur')}}" method="POST">
                @csrf
                <div class="modal-body">
                  <div class="mb-2">
                    <input type="text" class="form-control"  placeholder="identiteF" name="identiteF">
                  </div>
                  {{-- <div class="mb-2">
                    <input type="text" class="form-control"  placeholder="Prenom" name="PrenomF">
                  </div> --}}
                  <div class="mb-2">
                    <input type="text" class="form-control"  placeholder="Adresse" name="AdresseF">
                  </div>
                  <div>
                    <input type="text" class="form-control"  placeholder="Contact" name="ContactF">
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