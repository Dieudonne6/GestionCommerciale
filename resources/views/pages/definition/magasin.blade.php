@extends('layouts.master')
@section('content')

<div class="container-xxl">
  <div class="row justify-content-center">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col">
              <h4 class="card-title">Liste des Magasins</h4>
            </div>
            <div class="col-auto">
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMagasinModal">
                <i class="fa-solid fa-plus me-1"></i> Ajouter un Magasin
              </button>
            </div>
          </div>
        </div>

        @if(Session::has('status'))
        <div id="statusAlert" class="alert alert-success">
          {{ Session::get('status') }}
        </div>
        @endif
        @if(Session::has('erreur'))
        <div id="statusAlert" class="alert alert-danger">
          {{ Session::get('erreur') }}
        </div>
        @endif

        <div class="card-body pt-0">
          <div class="table-responsive">
            <table class="table mb-0" id="datatable_1">
              <thead class="table-light">
                <tr>
                  <th>Code</th>
                  <th>Libellé</th>
                  <th class="text-end">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($allmagasins as $magasin)
                <tr>
                  <td>{{ $magasin->codeMgs }}</td>
                  <td>{{ $magasin->libelleMgs }}</td>
                  <td class="text-end">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModifyMagasinModal{{ $magasin->idMgs }}">Modifier</button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteMagasinModal{{ $magasin->idMgs }}">Supprimer</button>
                  </td>
                </tr>

                <!-- Modal de modification -->
                <div class="modal fade" id="ModifyMagasinModal{{ $magasin->idMgs }}" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5">Modifier un Magasin</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <form action="{{ url('modifMagasin/'.$magasin->idMgs) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                          <div class="mb-2">
                            <input type="text" class="form-control" placeholder="Code" name="codeMgs" value="{{ $magasin->codeMgs }}" required>
                          </div>
                          <div>
                            <input type="text" class="form-control" placeholder="Libellé" name="libelleMgs" value="{{ $magasin->libelleMgs }}" required>
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

                <!-- Modal de suppression -->
                <div class="modal fade" id="deleteMagasinModal{{ $magasin->idMgs }}" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5">Confirmation de suppression</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        Êtes-vous sûr de vouloir supprimer ce magasin ?
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <form action="{{ url('suppMagasin/'.$magasin->idMgs) }}" method="POST">
                          @csrf 
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger">Confirmer</button>
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

        <!-- Modal d'ajout -->
        <div class="modal fade" id="addMagasinModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5">Ajouter un Magasin</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
              <form action="{{ url('ajouterMagasin') }}" method="POST">
                @csrf
                <div class="modal-body">
                  <div class="mb-2">
                    <input type="text" class="form-control" placeholder="Code" name="codeMgs" required>
                  </div>
                  <div>
                    <input type="text" class="form-control" placeholder="Libellé" name="libelleMgs" required>
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
</div>

@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
  var myModal = new bootstrap.Modal(document.getElementById('addMagasinModal'));
  @if ($errors->any())
  myModal.show();
  @endif
});
</script>
