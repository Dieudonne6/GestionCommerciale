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
                                <h4 class="card-title">Liste des categories</h4>

                            </div><!--end col-->
                            <div class="col-auto">
                                <form class="row g-2">

                                    <div class="col-auto">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#addCategoryModal" onclick="resetForm()"><i
                                                class="fa-solid fa-plus me-1"></i>
                                            Ajouter une catégorie</button>
                                    </div><!--end col-->
                                </form>
                            </div><!--end col-->
                        </div><!--end row-->
                    </div><!--end card-header-->
                    <div class="card-body pt-0">

                        <div class="table-responsive">
                            <table class="table mb-0 checkbox-all" id="datatable_1">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 16px;">
                                            <div class="form-check mb-0 ms-n1">
                                                <input type="checkbox" class="form-check-input" name="select-all"
                                                    id="select-all">
                                            </div>
                                        </th>
                                        <th class="text-center">Numéro</th>
                                        <th class="ps-0 text-center">Libellé</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categoriesF as $categoryF)
                                        <tr class="text-center">
                                            <td style="width: 16px;">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" name="check"
                                                        id="customCheck1">
                                                </div>
                                            </td>
                                            <td>{{ $categoryF->codeCatFour }}</td>
                                            <td>{{ $categoryF->libelle }}</td>
                                            <td>
                                                <!-- Bouton pour modifier (ouvre un modal) -->
                                                <!-- Bouton pour modifier -->
                                                <a href="{{ route('categoriesF.edit', $categoryF->idCatFour) }}"
                                                    class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#editCategoryModal{{ $categoryF->idCatFour }}"
                                                    style="margin-right: 24px; margin-left: 43px;">
                                                    Modifier
                                                </a>
                                                <!-- Formulaire pour supprimer une catégorie -->
                                                <form action="{{ route('categoriesF.destroy', $categoryF->idCatFour) }}"
                                                    class="btn btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $categoryF->idCatFour }}" method="POST"
                                                    style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <span>Supprimer</span>
                                                </form>
                                                <!-- <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                                                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a> -->
                                            </td>
                                        </tr>

                                        <!-- Modal de modification de catégorie -->
                                        <div class="modal fade" id="editCategoryModal{{ $categoryF->idCatFour }}"
                                            tabindex="-1"
                                            aria-labelledby="editCategoryModalLabel{{ $categoryF->idCatFour }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editCategoryModalLabel">
                                                            Modifier la catégorie
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <!-- Formulaire pour modifier la catégorie -->
                                                        <form method="POST"
                                                            action="{{ route('categoriesF.update', $categoryF->idCatFour) }}">
                                                            @csrf
                                                            @method('PUT')

                                                            <!-- Champ pour le code de la catégorie -->
                                                            <div class="mb-3">
                                                                <label for="codeCatFour" class="form-label">Numéro de la
                                                                    Catégorie</label>
                                                                <input type="text"
                                                                    class="form-control @error('codeCatFour') is-invalid @enderror"
                                                                    id="codeCatFour" name="codeCatFour"
                                                                    value="{{ $categoryF->codeCatFour }}" required>
                                                                @error('codeCatFour')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>

                                                            <!-- Libellé -->
                                                            <div class="mb-3">
                                                                <label for="libelle" class="form-label">Nom de la
                                                                    Catégorie</label>
                                                                <input type="text"
                                                                    class="form-control @error('libelle') is-invalid @enderror"
                                                                    id="libelle" name="libelle"
                                                                    value="{{ $categoryF->libelle }}" required>
                                                                @error('libelle')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Annuler</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary">Modifier</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Script pour afficher le modal après actualisation si erreurs -->
                                        @if (session('showModifyCategoryModal'))
                                            <script>
                                                document.addEventListener("DOMContentLoaded", function() {
                                                    var categoryId = "{{ session('showModifyCategoryModal') }}";
                                                    var myModal = new bootstrap.Modal(document.getElementById('editCategoryModal' + categoryId));
                                                    myModal.show();
                                                });
                                            </script>
                                        @endif

                                        <div class="modal fade" id="deleteModal{{ $categoryF->idCatFour }}" tabindex="-1"
                                            aria-labelledby="deleteModal{{ $categoryF->idCatFour }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">
                                                            Confirmation de suppression</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Êtes-vous sûr de vouloir supprimer cette categorie?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Annuler</button>
                                                        <form
                                                            action="{{ route('categoriesF.destroy', $categoryF->idCatFour) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" name="id"
                                                                value="{{ $categoryF->idCatFour }}">
                                                            <input type="submit" class="btn btn-danger"
                                                                value="Confirmer">
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
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div><!-- container -->
    <!-- Modal for adding category -->
    <!-- Modal d'ajout -->
    <!-- Modal d'ajout -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Ajouter une Catégorie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('categoriesF.store') }}" method="POST">
                        @csrf
                        <!-- Code de la catégorie -->
                        <div class="mb-3">
                            <label for="codeCatFour" class="form-label">Numéro de la Catégorie</label>
                            <input type="text" class="form-control @error('codeCatFour') is-invalid @enderror"
                                id="codeCatFour" name="codeCatFour" value="{{ old('codeCatFour') }}" required>
                            @error('codeCatFour')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Libellé -->
                        <div class="mb-3">
                            <label for="libelle" class="form-label">Nom de la Catégorie</label>
                            <input type="text" class="form-control @error('libelle') is-invalid @enderror"
                                id="libelle" name="libelle" value="{{ old('libelle') }}" required>
                            @error('libelle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script pour afficher le modal après actualisation si erreurs -->
    @if (session('showAddCategoryModal'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
                myModal.show();
            });
        </script>
    @endif
    
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
