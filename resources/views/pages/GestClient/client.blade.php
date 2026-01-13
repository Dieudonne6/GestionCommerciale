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
                  <th class="ps-0">Nom</th>
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

                            {{-- 1) Type de client --}}
                            <div class="mb-3">
                              <label for="clientType{{ $client->idC }}">Type de Client</label>
                              <select id="clientType{{ $client->idC }}" name="type"
                                      class="form-control @error('type') is-invalid @enderror" required>
                                <option value="physique" {{ $client->type == 'physique' ? 'selected' : '' }}>
                                  Personne physique
                                </option>
                                <option value="morale" {{ $client->type == 'morale' ? 'selected' : '' }}>
                                  Personne morale
                                </option>
                              </select>
                              @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="row">
                              {{-- 2) IFU --}}
                              <div class="col-md-6 mb-2" id="ifuField{{ $client->idC }}">
                                <label for="IFU{{ $client->idC }}">IFU</label>
                                <input type="number" class="form-control @error('IFU') is-invalid @enderror"
                                       id="IFU{{ $client->idC }}" name="IFU"
                                       value="{{ $client->IFU }}" placeholder="IFU" required>
                                @error('IFU')<div class="invalid-feedback">{{ $message }}</div>@enderror
                              </div>
                              
                              {{-- 3) Nom & Prénoms --}}
                              <div class="col-md-6 mb-2" id="nomField{{ $client->idC }}">
                                <label for="nom{{ $client->idC }}">Nom </label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                       id="nom{{ $client->idC }}" name="nom"
                                       value="{{ $client->nom }}" placeholder="Nom " required>
                                @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
<div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" >
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
            <!-- Nouveau champ Type de client -->
            <div class="col-md-12 mb-3">
              <label for="clientType">Type de Client</label>
              <select 
              class="form-control @error('type') is-invalid @enderror" 
              id="clientType" 
              name="type" 
              required
            >
              <!-- Option vide : uniquement quand on veut forcer l'utilisateur à choisir manuellement -->
              <option value="" disabled {{ old('type', 'physique') ? '' : 'selected' }}>
                Sélectionner un type
              </option>
            
              <!-- On indique que 'physique' est la valeur par défaut via old('type','physique') -->
              <option value="physique" {{ old('type', 'physique') === 'physique' ? 'selected' : '' }}>
                Personne physique
              </option>
            
              <option value="morale" {{ old('type', 'physique') === 'morale' ? 'selected' : '' }}>
                Personne morale
              </option>
            </select>
              @error('type')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          
          <div class="row">
          <!-- IFU enveloppé pour être cachable -->
          <div class="col-md-6 mb-3" id="ifuField">
            <label for="ifu">IFU</label>
            <input 
              type="number" 
              class="form-control @error('IFU') is-invalid @enderror" 
              id="ifu" 
              name="IFU" 
              value="{{ old('IFU') }}" 
              required
            >
            @error('IFU')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>



            <!-- Nouveau : conteneur du nom -->
            <div class="col-md-6 mb-3" id="nomField">
              <label for="nom">Nom </label>
              <input
                type="text"
                class="form-control @error('nom') is-invalid @enderror"
                id="nom"
                name="nom"
                value="{{ old('nom') }}"
                required
              >
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

{{-- Script pour cacher le champ IFU  --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const clientType = document.getElementById('clientType');
    const ifuField   = document.getElementById('ifuField');
    const nomField   = document.getElementById('nomField');
  
    function toggleFields() {
      const isPhysique = clientType.value === 'physique';
  
      // 1) IFU
      if (isPhysique) {
        ifuField.classList.add('d-none');
        ifuField.querySelector('input').required = false;
      } else {
        ifuField.classList.remove('d-none');
        ifuField.querySelector('input').required = true;
      }
  
      // 2) Nom : full width si Physique, 6-col sinon
      if (isPhysique) {
        nomField.classList.remove('col-md-6');
        nomField.classList.add('col-md-12');
      } else {
        nomField.classList.remove('col-md-12');
        nomField.classList.add('col-md-6');
      }
    }
  
    // Au chargement (pour old() ou défaut)
    toggleFields();
  
    // À chaque changement
    clientType.addEventListener('change', toggleFields);
  });
</script>


<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Parcours de chacun de vos modals édit
    document.querySelectorAll('div[id^="editClientModal"]').forEach(modalEl => {
      const idC = modalEl.id.replace('editClientModal','');
      const selectType = modalEl.querySelector('#clientType' + idC);
      const ifuField   = modalEl.querySelector('#ifuField' + idC);
      const nomField   = modalEl.querySelector('#nomField' + idC);
  
      function toggleEditFields() {
        const isPhys = selectType.value === 'physique';
        // IFU
        if (isPhys) {
          ifuField.classList.add('d-none');
          ifuField.querySelector('input').required = false;
        } else {
          ifuField.classList.remove('d-none');
          ifuField.querySelector('input').required = true;
        }
        // Nom plein écran ou moitié
        nomField.classList.toggle('col-md-12', isPhys);
        nomField.classList.toggle('col-md-6', !isPhys);
      }
  
      // Au chargement du modal (en cas de showAddClientModal)
      toggleEditFields();
  
      // À chaque changement
      selectType.addEventListener('change', toggleEditFields);
  
      // Aussi, si vous préférez vous assurer du bon état à l'ouverture :
      modalEl.addEventListener('shown.bs.modal', toggleEditFields);
    });
  });
</script>
  
  


{{-- Show add modal when needed --}}
@if (session('showAddClientModal'))
<script>
document.addEventListener("DOMContentLoaded", function() {
  var modalEl = document.getElementById('addClientModal');
  if (modalEl) {
    // s'assurer qu'aucun modal visible n'empêche l'ouverture propre
    var shown = document.querySelector('.modal.show');
    if (shown && shown !== modalEl) {
      bootstrap.Modal.getInstance(shown)?.hide();
    }

    var myModal = bootstrap.Modal.getOrCreateInstance(modalEl);
    myModal.show();
  } else {
    console.warn('addClientModal introuvable');
  }
});
</script>
@endif

{{-- Show edit modal for a specific client when needed --}}
@if (session('showModifyClientModal'))
<script>
document.addEventListener("DOMContentLoaded", function() {
  var clientId = "{{ session('showModifyClientModal') }}"; // doit être l'id numérique (ex: 12)
  var modalEl = document.getElementById('editClientModal' + clientId);
  if (modalEl) {
    // fermer tout modal déjà ouvert (évite conflit backdrop)
    var shown = document.querySelector('.modal.show');
    if (shown && shown !== modalEl) {
      bootstrap.Modal.getInstance(shown)?.hide();
    }

    // Crée ou récupère l'instance du modal et l'ouvre proprement
    var myModal = bootstrap.Modal.getOrCreateInstance(modalEl);
    myModal.show();

    // Optionnel : forcer le focus dans le modal
    modalEl.addEventListener('shown.bs.modal', function () {
      modalEl.querySelector('input,select,textarea,button')?.focus();
    });
  } else {
    console.warn('Modal edition introuvable :', 'editClientModal' + clientId);
  }
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
