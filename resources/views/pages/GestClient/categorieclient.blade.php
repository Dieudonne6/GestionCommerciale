@extends('layouts.master')

@section('content')
<!-- Styles pour personnaliser l'en-tête des modals -->
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
              <h4 class="card-title">Gestion des Catégories Clients</h4>
            </div>
            <div class="col-auto">
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fa-solid fa-plus me-1"></i> Ajouter Catégorie
              </button>
            </div>
          </div>
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

        <!-- Tableau des catégories -->
        <div class="card-body pt-0">
          <div class="table-responsive">
            <table class="table mb-0" id="datatable_1">
              <thead class="table-light">
                <tr>
                  <th>No</th>
                  <th>Code</th>
                  <th>Libellé</th>
                  <th class="text-end">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($categorie_clients as $categorie)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $categorie->codeCatCl }}</td>
                    <td>{{ $categorie->libelle }}</td>
                    <td class="text-end">
                      <!-- Bouton de modification -->
                      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modifyCategoryModal{{ $categorie->idCatCl }}">
                        Modifier
                      </button>
                      <!-- Bouton de suppression -->
                      <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal{{ $categorie->idCatCl }}">
                        Supprimer
                      </button>
                    </td>
                  </tr>

                  <!-- Modal de modification -->
                  <div class="modal fade" id="modifyCategoryModal{{ $categorie->idCatCl }}" tabindex="-1" aria-labelledby="modifyCategoryModalLabel{{ $categorie->idCatCl }}" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="modifyCategoryModalLabel{{ $categorie->idCatCl }}">Modifier Catégorie Client</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('categorieclient.modifier', $categorie->idCatCl) }}" method="POST">
                          @csrf
                          @method('PUT')
                          <div class="modal-body">
                            <div class="mb-2">
                              <label for="codeCatCl{{ $categorie->idCatCl }}" class="form-label">Code</label>
                              <input type="text" class="form-control @error('codeCatCl') is-invalid @enderror" id="codeCatCl{{ $categorie->idCatCl }}" name="codeCatCl" value="{{ old('codeCatCl', $categorie->codeCatCl) }}" required>
                              @error('codeCatCl')
                                <div class="invalid-feedback">{{ $message }}</div>
                              @enderror
                            </div>
                            <div class="mb-2">
                              <label for="libelle{{ $categorie->idCatCl }}" class="form-label">Libellé</label>
                              <input type="text" class="form-control @error('libelle') is-invalid @enderror" id="libelle{{ $categorie->idCatCl }}" name="libelle" value="{{ old('libelle', $categorie->libelle) }}" required>
                              @error('libelle')
                                <div class="invalid-feedback">{{ $message }}</div>
                              @enderror
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
                  <div class="modal fade" id="deleteCategoryModal{{ $categorie->idCatCl }}" tabindex="-1" aria-labelledby="deleteCategoryModalLabel{{ $categorie->idCatCl }}" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="deleteCategoryModalLabel{{ $categorie->idCatCl }}">Confirmation de suppression</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          Êtes-vous sûr de vouloir supprimer cette catégorie client ?
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                          <form action="{{ route('categorieclient.supprimer', $categorie->idCatCl) }}" method="POST">
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
        <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Ajouter Catégorie Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form action="{{ route('categorieclient.ajouter') }}" method="POST">
                @csrf
                <div class="modal-body">
                  <div class="mb-2">
                    <label for="codeCatCl" class="form-label">Code</label>
                    <input type="text" class="form-control @error('codeCatCl') is-invalid @enderror" id="codeCatCl" name="codeCatCl" value="{{ old('codeCatCl') }}" required>
                    @error('codeCatCl')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="mb-2">
                    <label for="libelle" class="form-label">Libellé</label>
                    <input type="text" class="form-control @error('libelle') is-invalid @enderror" id="libelle" name="libelle" value="{{ old('libelle') }}" required>
                    @error('libelle')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
        <!-- Fin Modal d'ajout -->

      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Ouvre automatiquement le modal d'ajout en cas d'erreurs de validation
    @if ($errors->any())
      var addCategoryModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
      addCategoryModal.show();
    @endif

    // Réinitialisation des erreurs de validation lors de la fermeture des modals
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
      
      // Optionnel : supprimer les erreurs de session si nécessaire
      @if(Session::has('error'))
        @php
          session()->forget('error');
        @endphp
      @endif
    }
  });
</script>
@endsection
