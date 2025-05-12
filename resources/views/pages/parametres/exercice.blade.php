@extends('layouts.master')
@section('content')

    <div class="container">

        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Listes des exercices</h4>
                            </div><!--end col-->

                            @if (Session::has('status'))
                                <br>
                                <div class="alert alert-success alert-dismissible">
                                    {{ Session::get('status') }}
                                </div>
                            @endif

                            @if (Session::has('erreur'))
                                <br>
                                <div class="alert alert-danger alert-dismissible">
                                    {{ Session::get('erreur') }}
                                </div>
                            @endif
                            <div class="col-auto">
                                <div class="col-auto">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addBoardModal"><i class="fa-solid fa-plus me-1"></i> Ajouter un
                                        Exercice</button>
                                </div><!--end col-->
                            </div><!--end col-->
                        </div><!--end row-->
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table mb-0 checkbox-all" id="datatable_1">
                                <thead class="table-light">
                                    <tr>
                                        <th>Annee</th>
                                        <th>Date debut</th>
                                        <th>Date fin</th>
                                        <th>Etat</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($exercices as $exercice)
                                        <tr>
                                            <td>
                                                {{ $exercice->annee }}
                                            </td>

                                            {{-- <td >
                    <p class="d-inline-block align-middle mb-0">
                      <span class="font-13 fw-medium">{{ $allfournisseur->PrenomF }}</span>
                    </p>
                  </td> --}}
                                            <td>{{ $exercice->dateDebut }}</td>
                                            <td>{{ $exercice->dateFin }}</td>
                                            <td>
                                                @if ($exercice->statutExercice == 1)
                                                    Actif
                                                @else
                                                    Non Actif
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('activerExercice', $exercice->idExercice) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-primary">Activer</button>
                                                </form>
                                                {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModifyBoardModal{{$exercice->idE}}"> Activer </button> --}}
                                                {{-- <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBoardModal{{$allfournisseur->idF}}"> Supprimer</button> --}}
                                            </td>
                                        </tr>
                                        {{-- <div class="modal fade" id="ModifyBoardModal{{ $exercice->idE }}" tabindex="-1" aria-labelledby="ModifyBoardModal{{ $exercice->idE }}" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Modifier un Fournisseur</h1>
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
                      <?php $error = Session::get('error'); ?>
            
                      @if (Session::has('error'))
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
                </div> --}}
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal fade" id="addBoardModal" tabindex="-1" aria-labelledby="addBoardModal"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Ajouter un Exercice</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
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

                                <form action="{{ route('ajouterExercice') }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-2 d-flex align-items-center">
                                          <label class="me-2" style="width: 80px">Année</label>
                                          <input type="text" class="form-control" placeholder="annee" name="annee" required>
                                        </div>
                                        {{-- <div class="mb-2">
                                          <input type="text" class="form-control"  placeholder="Prenom" name="PrenomF">
                                        </div> --}}
                                        <div class="mb-2 d-flex align-items-center">
                                          <label class="me-2" style="width: 80px">Date début</label>
                                          <input type="date" class="form-control" placeholder="dateDebut" name="dateDebut" required>
                                        </div>
                                        <div class="d-flex align-items-center">
                                          <label class="me-2" style="width: 80px">Date fin</label>
                                          <input type="date" class="form-control" placeholder="dateFin" name="dateFin" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Annuler</button>
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
