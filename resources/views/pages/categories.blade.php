@extends('layouts.master')
@section('content')
            <div class="container-xxl">
                <div class="row">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h1 class="mb-2">Gestion des Categories</h1>
                                <div class="row align-items-center">
                                    <div class="col">
                                        
                                    </div><!--end col-->
                                    <div class="col-auto">
                                        <form class="row g-2">
                                            <div class="col-auto">

                                                <div class="dropdown-menu dropdown-menu-start">
                                                    <div class="p-2">
                                                        <div class="form-check mb-2">
                                                            <input type="checkbox" class="form-check-input" checked
                                                                id="filter-all">
                                                            <label class="form-check-label" for="filter-all">
                                                                Tous
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input type="checkbox" class="form-check-input" checked
                                                                id="filter-one">
                                                            <label class="form-check-label" for="filter-one">
                                                                Mode
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input type="checkbox" class="form-check-input" checked
                                                                id="filter-two">
                                                            <label class="form-check-label" for="filter-two">
                                                                Plante
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input type="checkbox" class="form-check-input" checked
                                                                id="filter-three">
                                                            <label class="form-check-label" for="filter-three">
                                                                Jouet
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input type="checkbox" class="form-check-input" checked
                                                                id="filter-four">
                                                            <label class="form-check-label" for="filter-four">
                                                                Gadget
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input type="checkbox" class="form-check-input" checked
                                                                id="filter-five">
                                                            <label class="form-check-label" for="filter-five">
                                                                Aliment
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" checked
                                                                id="filter-six">
                                                            <label class="form-check-label" for="filter-six">
                                                                Boisson
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!--end col-->

                                            <div class="col-auto">
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#addCategoryModal"><i class="fa-solid fa-plus me-1"></i>
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
                                                <th class="text-center">Code</th>
                                                <th class="ps-0 text-center">Nom Catégorie</th>
                                                <th class="text-center">Image</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($categories as $category)
                                                <tr class="text-center">
                                                    <td style="width: 16px;">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" name="check"
                                                                id="customCheck1">
                                                        </div>
                                                    </td>
                                                    <td>{{ $category->idC }}</td>
                                                    <td>{{ $category->NomC }}</td>
                                                    <td class="ps-0">
                                                        <img src="{{ asset('storage/' . $category->imgC) }}"
                                                            alt="{{ $category->NomC }}" height="40">
                                                    </td>
                                                    <td>


                                                        <!-- Bouton pour modifier (ouvre un modal) -->
                                                        <!-- Bouton pour modifier -->
                                                        <a href="{{ route('categories.edit', $category->idC) }}"
                                                            class="btn btn-primary" data-bs-toggle="modal"
                                                            data-bs-target="#editCategoryModal{{ $category->idC }}"
                                                            style="margin-right: 24px; margin-left: 43px;">
                                                            Modifier
                                                        </a>
                                                        <!-- Formulaire pour supprimer une catégorie -->
                                                        <form action="{{ route('categories.destroy', $category->idC) }}"
                                                            class="btn btn-danger" data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal{{$category->idC}}"
                                                            method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                                <span>Supprimer</span>
                                                        </form>
                                                        <!-- <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                                            <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a> -->
                                                    </td>
                                                </tr>

                                                            <!-- Modal de modification de catégorie -->
            <div class="modal fade" id="editCategoryModal{{ $category->idC }}" tabindex="-1"
                aria-labelledby="editCategoryModalLabel{{ $category->idC }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editCategoryModalLabel">Modifier la catégorie
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Formulaire pour modifier la catégorie -->
                            <form method="POST" action="{{ route('categories.update', $category->idC) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <!-- Champ pour le nom de la catégorie -->
                                <div class="mb-3">
                                    <label for="editCategoryName" class="form-label">Nom de la catégorie</label>
                                    <input type="text" class="form-control" id="editCategoryName" name="categoryName"
                                        value="{{ $category->NomC }}" required>
                                </div>

                                <!-- Champ pour télécharger une nouvelle image (optionnel) -->
                                <div class="mb-3">
                                    <label for="editCategoryImage" class="form-label">Nouvelle image</label>
                                    <input type="file" class="form-control" id="editCategoryImage"
                                        name="categoryImage" accept="image/*">
                                </div>


                                <div class="mb-3">
                                    <label for="currentCategoryImage" class="form-label">Image actuelle</label>
                                    <br>
                                    <img src="{{ asset('storage/' . $category->imgC) }}" class="img-fluid"
                                        style="max-width: 200px; max-height: 200px;" />
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
                            <div class="modal fade" id="deleteModal{{$category->idC}}" tabindex="-1" aria-labelledby="deleteModal{{$category->idC}}" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation de suppression</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        Êtes-vous sûr de vouloir supprimer cette categorie?
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <form action="{{ route('categories.destroy', $category->idC) }}" method="POST">
                          @csrf 
                          @method('DELETE')
                          <input type="hidden" name="id" value="{{$category->idC}}">
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
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->
            </div><!-- container -->
            <!-- Modal for adding category -->
            <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addCategoryModalLabel">Ajouter une catégorie</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="categoryName" class="form-label">Nom de la catégorie</label>
                                    <input type="text" class="form-control" id="categoryName" name="categoryName"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="categoryImage" class="form-label">Image</label>
                                    <input type="file" class="form-control" id="categoryImage" name="categoryImage"
                                        accept="image/*" required>
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
