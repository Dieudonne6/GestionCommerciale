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
                  <th class="ps-0">Nom et prénoms</th>
                  <th>Adresse</th>
                  <th>Contact</th>
                  <th class="text-end">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($allfournisseurs as $allfournisseur)
                <tr>
                  <td class="ps-0">
                    <p class="d-inline-block align-middle mb-0">
                      <span class="font-13 fw-medium">{{ $allfournisseur->NomF }}</span>
                    </p>
                  </td>
                  <td class="ps-0">
                    <p class="d-inline-block align-middle mb-0">
                      <span class="font-13 fw-medium">{{ $allfournisseur->PrenomF }}</span>
                    </p>
                  </td>
                  <td>{{ $allfournisseur->AdresseF }}</td>
                  <td>{{ $allfournisseur->ContactF }}</td>
                  <td class="text-end">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModifyBoardModal{{$allfournisseur->idF}}"> Modifier</button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBoardModal{{$allfournisseur->idF}}"> Supprimer</button>
                  </td>
                </tr>
                <div class="modal fade" id="ModifyBoardModal{{$allfournisseur->idF}}" tabindex="-1" aria-labelledby="ModifyBoardModal{{$allfournisseur->idF}}" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Ajouter un Fournisseur</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      
                      <form action="{{url('/modifyfournisseur')}}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                          <div class="mb-2">
                            <input type="text" class="form-control"  placeholder="Nom" name="NomF" value="{{ $allfournisseur->NomF }}">
                          </div>
                          <div class="mb-2">
                            <input type="text" class="form-control"  placeholder="Prenom" name="PrenomF" value="{{ $allfournisseur->PrenomF }}">
                          </div>
                          <div class="mb-2">
                            <input type="text" class="form-control"  placeholder="Adresse" name="AdresseF" value="{{ $allfournisseur->AdresseF }}">
                          </div>
                          <div>
                            <input type="text" class="form-control"  placeholder="Contact" name="ContactF" value="{{ $allfournisseur->ContactF }}">
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                          <button type="button" class="btn btn-primary">Envoyer</button>
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
                        Êtes-vous sûr de vouloir supprimer cette série ?
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <form action="{{ url('/supprimerforunisseur')}}" method="POST">
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
        <div class="modal fade" id="addBoardModal" tabindex="-1" aria-labelledby="addBoardModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Ajouter un Fournisseur</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              
              <form action="{{url('/savepaiementcontrat')}}" method="POST">
                @csrf
                <div class="modal-body">
                  <div class="mb-2">
                    <input type="text" class="form-control"  placeholder="Nom" name="NomF">
                  </div>
                  <div class="mb-2">
                    <input type="text" class="form-control"  placeholder="Prenom" name="PrenomF">
                  </div>
                  <div class="mb-2">
                    <input type="text" class="form-control"  placeholder="Adresse" name="AdresseF">
                  </div>
                  <div>
                    <input type="text" class="form-control"  placeholder="Contact" name="ContactF">
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                  <button type="button" class="btn btn-primary">Envoyer</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  @endsection