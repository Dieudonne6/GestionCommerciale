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
  <div class="row justify-content-center">
    <div class="col-12">
      <div class="card">
        <!-- En-tête -->
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col">
              <h4 class="card-title">Liste des Clients</h4>
            </div>

            <!-- Messages flash -->
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
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClientModal">
                <i class="fa-solid fa-plus me-1"></i> Ajouter un Client
              </button>
            </div>
          </div>
        </div>

        <!-- Tableau des clients -->
        <div class="card-body pt-0">
          <div class="table-responsive">
            <table class="table mb-0" id="datatable_1">
              <thead class="table-light">
                <tr>
                  <th>No</th>
                  <th class="ps-0">Nom & Prénoms</th>
                  <th>Adresse</th>
                  <th>Contact</th>
                  <th>Email</th>
                  <th class="text-end">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($clients as $client)
                  <tr>
                    <td>
                      <p class="d-inline-block align-middle mb-0">
                        <span class="font-13 fw-medium">{{ $loop->iteration }}</span>
                      </p>
                    </td>
                    <td>{{ $client->nom }}</td>
                    <td>{{ $client->adresse }}</td>
                    <td>{{ $client->telephone }}</td>
                    <td>{{ $client->mail }}</td>
                    <td class="text-end">
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
                          <h1 class="modal-title fs-5" id="editClientModalLabel{{ $client->idC }}">Modifier un Client</h1>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('clients.update', $client->idC) }}" method="POST">
                          @csrf
                          @method('PUT')
                          <div class="modal-body">
                            <div class="row">
                              <div class="col-md-6 mb-2">
                                <label>IFU</label>
                                <input type="number" class="form-control @error('IFU') is-invalid @enderror" name="IFU" placeholder="IFU" value="{{ old('IFU', $client->IFU) }}" required>
                                @error('IFU')
                                  <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                              </div>
                              <div class="col-md-6 mb-2">
                                <label>Nom & Prénoms</label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror" name="nom" placeholder="Nom & Prénoms" value="{{ old('nom', $client->nom) }}" required>
                                @error('nom')
                                  <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                              </div>
                            </div>

                            <div class="row">
                              <div class="col-md-6 mb-2">
                                <label>Adresse</label>
                                <input type="text" class="form-control @error('adresse') is-invalid @enderror" name="adresse" placeholder="Adresse" value="{{ old('adresse', $client->adresse) }}" required>
                                @error('adresse')
                                  <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                              </div>
                              <div class="col-md-6 mb-2">
                                <label>Téléphone</label>
                                <input type="text" class="form-control @error('telephone') is-invalid @enderror" name="telephone" placeholder="Téléphone" value="{{ old('telephone', $client->telephone) }}" required>
                                @error('telephone')
                                  <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                              </div>
                            </div>

                            <div class="row">
                              <div class="col-md-6 mb-2">
                                <label>Email</label>
                                <input type="email" class="form-control @error('mail') is-invalid @enderror" name="mail" placeholder="Email" value="{{ old('mail', $client->mail) }}" required>
                                @error('mail')
                                  <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                              </div>
                              <div class="col-md-6 mb-2">
                                <label>Catégorie Client</label>
                                <select class="form-control" name="idCatCl" required>
                                  <option value="" disabled>Sélectionner une catégorie</option>
                                  @foreach ($categories as $categorie)
                                    <option value="{{ $categorie->idCatCl }}" {{ old('idCatCl', $client->idCatCl) == $categorie->idCatCl ? 'selected' : '' }}>
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
                          <h1 class="modal-title fs-5" id="deleteClientModalLabel{{ $client->idC }}">Confirmation de suppression</h1>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          Êtes-vous sûr de vouloir supprimer ce client ?
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                          <form action="{{ route('clients.destroy', $client->idC) }}" method="POST">
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
      </div>
    </div>
  </div>
</div>

<!-- Modal d'ajout -->
<div class="modal fade @if ($errors->any()) show @endif" 
     id="addClientModal" 
     tabindex="-1" 
     aria-labelledby="addClientModalLabel" 
     @if ($errors->any()) style="display: block;" @endif>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="addClientModalLabel">Ajouter un Client</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('clients.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          {{-- @if ($errors->any())
            <div class="alert alert-danger alert-dismissible">
              <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif --}}

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="ifu" class="form-label">IFU</label>
              <input type="number" class="form-control @error('IFU') is-invalid @enderror" id="ifu" name="IFU" value="{{ old('IFU') }}" required>
              @error('IFU')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="nom" class="form-label">Nom & Prénoms</label>
              <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" placeholder="Nom & Prénoms" value="{{ old('nom') }}" required>
              @error('nom')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="adresse" class="form-label">Adresse</label>
              <input type="text" class="form-control @error('adresse') is-invalid @enderror" id="adresse" name="adresse" placeholder="Adresse" value="{{ old('adresse') }}" required>
              @error('adresse')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="telephone" class="form-label">Téléphone</label>
              <input type="text" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" placeholder="Téléphone" value="{{ old('telephone') }}" required>
              @error('telephone')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control @error('mail') is-invalid @enderror" id="email" name="mail" placeholder="Email" value="{{ old('mail') }}" required>
              @error('mail')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="idCatCl" class="form-label">Catégorie Client</label>
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

@section('scripts')
<!-- Script pour ouvrir automatiquement le modal concerné en cas d'erreurs -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    @if ($errors->any())
      var modalId = "{{ session('errorModalId') }}"; // Récupère l'ID du modal avec erreurs
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

<!-- Script pour réinitialiser les erreurs lors de la fermeture ou de l'annulation d'un modal -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Sélectionne tous les modals
    var modals = document.querySelectorAll('.modal');

    modals.forEach(function(modal) {
      // Lors de la fermeture du modal via le bouton "Fermer"
      modal.addEventListener('hidden.bs.modal', function () {
        resetModalErrors(modal);
      });

      // Lors du clic sur le bouton "Annuler"
      var cancelButton = modal.querySelector('.btn-secondary');
      if (cancelButton) {
        cancelButton.addEventListener('click', function () {
          resetModalErrors(modal);
        });
      }
    });

    // Fonction de réinitialisation des erreurs dans le modal
    function resetModalErrors(modal) {
      modal.querySelectorAll('.invalid-feedback').forEach(function(errorElement) {
        errorElement.textContent = ''; // Supprime le texte des erreurs
      });
      modal.querySelectorAll('.form-control').forEach(function(inputField) {
        inputField.classList.remove('is-invalid'); // Retire la classe d'erreur
      });
      
      // Efface les erreurs dans la session si nécessaire
      @if(Session::has('error'))
        @php session()->forget('error'); @endphp
      @endif
    }
  });
</script>
@endsection
