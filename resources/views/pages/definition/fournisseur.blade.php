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
  
  <div class="row">
    @if (session('success'))
    <div class="alert alert-success alert-dismissible">
        {{ session('success') }}
    </div>
    @endif
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
                  <th >Numéro</th>
                  {{-- <th >Prénoms</th> --}}
                  <th class="text-center">IFU</th>
                  <th class="text-center">Nom</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($allfournisseurs as $allfournisseur)
                <tr class="text-center">
                  <td >
                    <p class="d-inline-block align-middle mb-0">
                      <span class="font-13 fw-medium">{{ $allfournisseur->idF }}</span>
                    </p>
                  </td>
                  {{-- <td >
                    <p class="d-inline-block align-middle mb-0">
                      <span class="font-13 fw-medium">{{ $allfournisseur->PrenomF }}</span>
                    </p>
                  </td> --}}
                  <td >{{ $allfournisseur->IFU }}</td>
                  <td >{{ $allfournisseur->nom }}</td>
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
              
                          {{-- @if ($errors->any())
                              <div class="alert alert-danger alert-dismissible">
                                  <ul>
                                      @foreach ($errors->all() as $error)
                                          <li>{{ $error }}</li>
                                      @endforeach
                                  </ul>
                              </div>
                          @endif --}}
              
                          @if (Session::has('error'))
                              <div class="alert alert-danger alert-dismissible">
                                  {{ Session::get('error') }}
                              </div>
                          @endif
              
                          <form action="{{ route('fournisseur.update', $allfournisseur->idF) }}" method="POST">
                              @csrf
                              @method('PUT')
              
                              <div class="modal-body">
                                  <div class="form-group">
                                      <label for="IFU">IFU</label>
                                      <input type="text" class="form-control @error('IFU') is-invalid  @enderror" name="IFU" value="{{ old('IFU', $allfournisseur->IFU) }}">
                                      @error('IFU')
                                          <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>
              
                                  <div class="form-group">
                                      <label for="nom">Nom du fournisseur</label>
                                      <input type="text" class="form-control @error('nom') is-invalid  @enderror" name="nom" value="{{ old('nom', $allfournisseur->nom) }}">
                                      @error('nom')
                                          <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>
              
                                  <div class="form-group">
                                      <label for="adresse">Adresse du fournisseur</label>
                                      <input type="text" class="form-control @error('adresse') is-invalid  @enderror" name="adresse" value="{{ old('adresse', $allfournisseur->adresse) }}">
                                      @error('adresse')
                                          <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>
              
                                  <div class="form-group">
                                      <label for="telephone">Téléphone</label>
                                      <input type="text" class="form-control @error('telephone') is-invalid  @enderror" name="telephone" value="{{ old('telephone', $allfournisseur->telephone) }}">
                                      @error('telephone')
                                          <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>
              
                                  <div class="form-group">
                                      <label for="mail">Email</label>
                                      <input type="email" class="form-control @error('mail') is-invalid  @enderror" name="mail" value="{{ old('mail', $allfournisseur->mail) }}">
                                      @error('mail')
                                          <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                  </div>
              
                                  <div class="form-group">
                                      <label for="categorie">Catégorie de Fournisseur</label>
                                      <select name="idCatFour" class="form-control @error('idCatFour') is-invalid  @enderror">
                                          <option value="0" {{ old('idCatFour', $allfournisseur->idCatFour) == 0 ? 'selected' : '' }}>Aucune</option>
                                          @foreach ($categoriesF as $categorieF)
                                              <option value="{{ $categorieF->idCatFour }}" {{ old('idCatFour', $allfournisseur->idCatFour) == $categorieF->idCatFour ? 'selected' : '' }}>
                                                  {{ $categorieF->libelle }}
                                              </option>
                                          @endforeach
                                      </select>
                                      @error('idCatFour')
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
              
                                  <!-- Script pour afficher le modal après actualisation si erreurs -->
                  @if (session('showModifyFournisseurModal'))
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            var fournisseurId = "{{ session('showModifyFournisseurModal') }}";
                            var myModal = new bootstrap.Modal(document.getElementById('ModifyBoardModal' + fournisseurId));
                            myModal.show();
                        });
                    </script>
                  @endif
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

        <div data-modal="addBoard" class="modal fade" tabindex="-1" role="dialog" id="addBoardModal" aria-labelledby="addBoardLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="addBoardLabel">Ajouter un Fournisseur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
            {{--     @if ($errors->any())
                  <div class="alert alert-danger alert-dismissible">
                    <ul>
                      @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif --}}
                <form action="{{ route('fournisseurs.ajouterFournisseur') }}" method="POST">
                  @csrf
                  @method('POST')
        
                  <div class="form-group">
                    <label for="IFU">IFU</label>
                    <input type="text" class="form-control @error('IFU') is-invalid  @enderror" name="IFU" value="{{ old('IFU') }}" required>
                    @error('IFU')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
        
                  <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" class="form-control @error('nom') is-invalid  @enderror" name="nom" value="{{ old('nom') }}" required>
                    @error('nom')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
        
                  <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <input type="text" class="form-control @error('adresse') is-invalid  @enderror" name="adresse" value="{{ old('adresse') }}" required>
                    @error('adresse')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
        
                  <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" class="form-control @error('telephone') is-invalid  @enderror" name="telephone" value="{{ old('telephone') }}" required>
                    @error('telephone')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
        
                  <div class="form-group">
                    <label for="mail">Email</label>
                    <input type="email" class="form-control @error('mail')  is-invalid @enderror" name="mail" value="{{ old('mail') }}" required>
                    @error('mail')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
        
                  <div class="form-group">
                    <label for="categorie">Catégorie de Fournisseur</label>
                    <select name="idCatFour" class="form-control @error('idCatFour') is-invalid  @enderror">
                      <option value="0">Aucune</option>
                      @foreach ($categoriesF as $categorieF)
                        <option value="{{ $categorieF->idCatFour }}" {{ old('idCatFour') == $categorieF->idCatFour ? 'selected' : '' }}>
                          {{ $categorieF->libelle }}
                        </option>
                      @endforeach
                    </select>
                    @error('idCatFour')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
        
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        
            <!-- Script pour afficher le modal après actualisation si erreurs -->
    @if (session('showAddFournisseurModal'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var myModal = new bootstrap.Modal(document.getElementById('addBoardModal'));
            myModal.show();
        });
    </script>
@endif

      </div>
    </div>
  </div>
  
  @endsection

  @section('scripts')
  <script>
      document.addEventListener("DOMContentLoaded", function () {
        document.addEventListener("hidden.bs.modal", function () {
            setTimeout(() => {
                let backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
                document.body.classList.remove('modal-open');
            }, 200);
        });
    });
  </script>
  @endsection
  
  @section('styles')
<style>
    #datatable_1 td,
    #datatable_1 th {
        text-align: center;
    }

    /* Centrer les images dans les cellules */
    #datatable_1 td img {
        display: block;
        margin: 0 auto;
    }

    /* Centrer le texte dans les liens aussi */
    #datatable_1 td a {
        display: inline-block;
        text-align: center;
    }

    .modal-content {
        border-radius: 8px;
    }
</style>
@endsection
{{-- @section('scripts') --}}

{{-- <script>
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

@endsection --}}



