@extends('layouts.master')
@section('content')

<div class="container-xxl">
  <div class="row justify-content-center">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col">
              <h4 class="card-title">Listes des Clients</h4>
            </div><!--end col-->
            <div class="col-auto">
              <div class="col-auto">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBoardModal"><i class="fa-solid fa-plus me-1"></i> Ajouter un Client</button>
              </div><!--end col-->
            </div><!--end col-->
          </div><!--end row-->
        </div>
        @if ($errors->any())
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif
        @if(Session::has('status'))
        <div id="statusAlert" class="alert alert-success">
          {{ Session::get('status')}}
        </div>
        @endif
        <div class="card-body pt-0">
          <div class="table-responsive">
            <table class="table mb-0 checkbox-all" id="datatable_1">
              <thead class="table-light">
                <tr>
                  <th class="ps-0">Nom</th>
                  <th>Prénoms</th>
                  <th>Adresse</th>
                  <th>Contact</th>
                  <th class="text-end">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($allclients as $allclient)
                <tr>
                  <td class="ps-0">
                    <p class="d-inline-block align-middle mb-0">
                      <span class="font-13 fw-medium">{{ $allclient->NomCl }}</span>
                    </p>
                  </td>
                  <td class="ps-0">
                    <p class="d-inline-block align-middle mb-0">
                      <span class="font-13 fw-medium">{{ $allclient->PrenomCl }}</span>
                    </p>
                  </td>
                  <td>{{ $allclient->AdresseCl }}</td>
                  <td>{{ $allclient->ContactCl }}</td>
                  <td class="text-end">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModifyBoardModal{{$allclient->idCl}}"> Modifier</button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBoardModal{{$allclient->idCl}}"> Supprimer</button>
                  </td>
                </tr>
                <div class="modal fade" id="ModifyBoardModal{{$allclient->idCl}}" tabindex="-1" aria-labelledby="ModifyBoardModal{{$allclient->idCl}}" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Ajouter un Client</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <form action="{{url('modifClient/'.$allclient->idCl)}}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                          <div class="mb-2">
                            <input type="text" class="form-control"  placeholder="Nom" name="NomCl" value="{{ $allclient->NomCl }}">
                          </div>
                          <div class="mb-2">
                            <input type="text" class="form-control"  placeholder="Prenom" name="PrenomCl" value="{{ $allclient->PrenomCl }}">
                          </div>
                          <div class="mb-2">
                            <input type="text" class="form-control"  placeholder="Adresse" name="AdresseCl" value="{{ $allclient->AdresseCl }}">
                          </div>
                          <div>
                            <input type="text" class="form-control"  placeholder="Contact" name="ContactCl" value="{{ $allclient->ContactCl }}">
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                          <button type="submit" class="btn btn-primary">Envoyer</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <div class="modal fade" id="deleteBoardModal{{$allclient->idCl}}" tabindex="-1" aria-labelledby="deleteBoardModal{{$allclient->idCl}}" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation de suppression</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        Êtes-vous sûr de vouloir supprimer cette client?
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <form action="{{ url('suppClient/'.$allclient->idCl)}}" method="POST">
                          @csrf 
                          @method('DELETE')
                          <input type="hidden" name="idCl" value="{{$allclient->idCl}}">
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
                <h1 class="modal-title fs-5" id="exampleModalLabel">Ajouter un Client</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              
              <form action="{{url('ajouterClient')}}" method="POST">
                @csrf
                <div class="modal-body">
                  <div class="mb-2">
                    <input type="text" class="form-control"  placeholder="Nom" name="NomCl">
                  </div>
                  <div class="mb-2">
                    <input type="text" class="form-control"  placeholder="Prenom" name="PrenomCl">
                  </div>
                  <div class="mb-2">
                    <input type="text" class="form-control"  placeholder="Adresse" name="AdresseCl">
                  </div>
                  <div>
                    <input type="text" class="form-control"  placeholder="Contact" name="ContactCl">
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                  <button type="submit" class="btn btn-primary">Envoyer</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  @endsection