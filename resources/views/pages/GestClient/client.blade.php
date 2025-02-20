@extends('layouts.master')

@section('content')
<!-- Styles personnalisés pour les modals -->
<style>
  .modal-header {
    background-color: #fff !important;
  }
  .modal-title {
    color: #000 !important;
  }
</style>

<div class="container-xxl">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <!-- En-tête -->
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col">
              <h4 class="card-title">Liste des Clients</h4>
            </div>
            <div class="col-auto">
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClientModal" onclick="resetForm()">
                <i class="fa-solid fa-plus me-1"></i> Ajouter un Client
              </button>
            </div>
          </div>
          <!-- Messages flash -->
          @if (Session::has('status'))
            <div class="alert alert-success alert-dismissible">
              {{ Session::get('status') }}
            </div>
          @endif
          @if (Session::has('erreur'))
            <div class="alert alert-danger alert-dismissible">
              {{ Session::get('erreur') }}
            </div>
          @endif
        </div>

        <!-- Tableau des clients -->
        <div class="card-body pt-0">
          <div class="table-responsive">
            <table class="table mb-0" id="datatable_1">
              <thead class="table-light">
                <tr>
                  <th>No</th>
                  <th class="ps-0">Nom &amp; Prénoms</th>
                  <th>Adresse</th>
                  <th>Contact</th>
                  <th>Email</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($clients as $client)
                  <tr class="text-center">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $client->nom }}</td>
                    <td>{{ $client->adresse }}</td>
                    <td>{{ $client->telephone }}</td>
                    <td>{{ $client->mail }}</td>
                    <td>
                      <!-- Bouton de modification -->
                      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editClientModal{{ $client->idC }}">
                        Modifier
                      </button>
                      <!-- Bouton de suppression -->
                      <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteClientModal{{ $client->idC }}">
                        Supprimer
                      </button>
                    </td>
                  </tr>

                  <!-- Modal de modification -->
                  <div class="modal fade" id="editClientModal{{ $client->idC }}" tabindex="-1" aria-labelledby="editClientModalLabel{{ $client->idC }}" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="editClientModalLabel{{ $client->idC }}">Modifier un Client</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('clients.update', $client->idC) }}" method="POST">
                          @csrf
                          @method('PUT')
                          <div class="modal-body">
                            <div class="row">
                              <div class="col-md-6 mb-2">
                                <label for="IFU{{ $client->idC }}">IFU</label>
                                <input type="number" class="form-control @error('IFU') is-invalid @enderror" id="IFU{{ $client->idC }}" name="IFU" placeholder="IFU" value="{{ $client->IFU }}" required>
                                @error('IFU')
                                  <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                              </div>
                              <div class="col-md-6 mb-2">
                                <label for="nom{{ $client->idC }}">Nom &amp; Prénoms</label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom{{ $client->idC }}" name="nom" placeholder="Nom &amp; Prénoms" value="{{ $client->nom }}" required>
                                @error('nom')
                                  <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6 mb-2">
                                <label for="adresse{{ $client->idC }}">Adresse</label>
                                <input type="text" class="form-control @error('adresse') is-invalid @enderror" id="adresse{{ $client->idC }}" name="adresse" placeholder="Adresse" value="{{ $client->adresse }}" required>
                                @error('adresse')
                                  <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                              </div>
                              <div class="col-md-6 mb-2">
                                <label for="telephone{{ $client->idC }}">Téléphone</label>
                                <input type="text" class="form-control @error('telephone') is-invalid @enderror" id="telephone{{ $client->idC }}" name="telephone" placeholder="Téléphone" value="{{ $client->telephone }}" required>
                                @error('telephone')
                                  <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6 mb-2">
                                <label for="mail{{ $client->idC }}">Email</label>
                                <input type="email" class="form-control @error('mail') is-invalid @enderror" id="mail{{ $client->idC }}" name="mail" placeholder="Email" value="{{ $client->mail }}" required>
                                @error('mail')
                                  <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                              </div>
                              <div class="col-md-6 mb-2">
                                <label for="idCatCl{{ $client->idC }}">Catégorie Client</label>
                                <select class="form-control" id="idCatCl{{ $client->idC }}" name="idCatCl" required>
                                  <option value="" disabled>Sélectionner une catégorie</option>
                                  @foreach ($categories as $categorie)
                                    <option value="{{ $categorie->idCatCl }}" {{ $client->idCatCl == $categorie->idCatCl ? 'selected' : '' }}>
                                      {{ $categorie->libelle }}
                                    </option>
                                  @endforeach
                                </select>
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

                  <!-- Modal de suppression -->
                  <div class="modal fade" id="deleteClientModal{{ $client->idC }}" tabindex="-1" aria-labelledby="deleteClientModalLabel{{ $client->idC }}" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="deleteClientModalLabel{{ $client->idC }}">Confirmation de suppression</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          Êtes-vous sûr de vouloir supprimer ce client ?
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                          <form action="{{ route('clients.destroy', $client->idC) }}" method="POST" style="display:inline-block;">
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
        <!-- Fin du tableau -->
      </div>
    </div>
  </div>
</div>

<!-- Modal d'ajout -->
<div class="modal fade @if ($errors->any()) show @endif" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" @if ($errors->any()) style="display: block;" @endif>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addClientModalLabel">Ajouter un Client</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('clients.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="ifu">IFU</label>
              <input type="number" class="form-control @error('IFU') is-invalid @enderror" id="ifu" name="IFU" value="{{ old('IFU') }}" required>
              @error('IFU')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="nom">Nom &amp; Prénoms</label>
              <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" placeholder="Nom &amp; Prénoms" value="{{ old('nom') }}" required>
              @error('nom')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="adresse">Adresse</label>
              <input type="text" class="form-control @error('adresse') is-invalid @enderror" id="adresse" name="adresse" placeholder="Adresse" value="{{ old('adresse') }}" required>
              @error('adresse')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="telephone">Téléphone</label>
              <input type="text" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" placeholder="Téléphone" value="{{ old('telephone') }}" required>
              @error('telephone')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="email">Email</label>
              <input type="email" class="form-control @error('mail') is-invalid @enderror" id="email" name="mail" placeholder="Email" value="{{ old('mail') }}" required>
              @error('mail')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="idCatCl">Catégorie Client</label>
              <select class="form-control" id="idCatCl" name="idCatCl" required>
                <option value="" disabled selected>Sélectionner une catégorie</option>
                @foreach ($categories as $categorie)
                  <option value="{{ $categorie->idCatCl }}" {{ old('idCatCl') == $categorie->idCatCl ? 'selected' : '' }}>
                    {{ $categorie->libelle }}
                  </option>
                @endforeach
              </select>
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
@endsection

{{-- Scripts pour afficher automatiquement les modals en cas d'erreur --}}
@if (session('showModifyClientModal'))
<script>
  document.addEventListener("DOMContentLoaded", function() {
      var clientId = "{{ session('showModifyClientModal') }}";
      var existingModal = document.querySelector('.modal.show');
      if (existingModal) {
          var modalInstance = bootstrap.Modal.getInstance(existingModal);
          modalInstance.hide();
      }
      var myModalElement = document.getElementById('editClientModal' + clientId);
      var myModal = new bootstrap.Modal(myModalElement);
      setTimeout(() => {
          myModal.show();
      }, 300);
  });
</script>
@endif

@if (session('showAddClientModal'))
<script>
  document.addEventListener("DOMContentLoaded", function() {
      var myModal = new bootstrap.Modal(document.getElementById('addClientModal'));
      myModal.show();
  });
</script>
@endif

@section('styles')
<style>
  #datatable_1 td,
  #datatable_1 th {
    text-align: center;
  }
  #datatable_1 td img {
    display: block;
    margin: 0 auto;
  }
  #datatable_1 td a {
    display: inline-block;
    text-align: center;
  }
  .modal-content {
    border-radius: 8px;
  }
</style>
@endsection
