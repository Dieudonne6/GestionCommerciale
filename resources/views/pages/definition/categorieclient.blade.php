@extends('layouts.master')
@section('content')

    <div class="container-xxl">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Gestion des Catégories Clients</h4>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#addCategoryModal">
                                    <i class="fa-solid fa-plus me-1"></i> Ajouter Catégorie
                                </button>
                            </div>
                        </div>
                    </div>

                    @if (Session::has('status'))
                        <div id="statusAlert" class="alert alert-success">
                            {{ Session::get('status') }}
                        </div>
                    @endif
                    @if (Session::has('erreur'))
                        <div id="statusAlert" class="alert alert-danger">
                            {{ Session::get('erreur') }}
                        </div>
                    @endif

                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table mb-0" id="categoriesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
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
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#ModifyCategoryModal{{ $categorie->idCatCl }}">
                                                    Modifier
                                                </button>
                                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteCategoryModal{{ $categorie->idCatCl }}">
                                                    Supprimer
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Modal de modification -->
                                        <div class="modal fade" id="ModifyCategoryModal{{ $categorie->idCatCl }}"
                                            tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Modifier Catégorie Client</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('categorieclient.modifier', $categorie->idCatCl) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-2">
                                                                <label for="codeCatCl">Code</label>
                                                                <input type="text" class="form-control"
                                                                    name="codeCatCl" value="{{ $categorie->codeCatCl }}" required>
                                                            </div>
                                                            <div>
                                                                <label for="libelle">Libellé</label>
                                                                <input type="text" class="form-control"
                                                                    name="libelle" value="{{ $categorie->libelle }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Annuler</button>
                                                            <button type="submit" class="btn btn-primary">Modifier</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal de suppression -->
                                        <div class="modal fade" id="deleteCategoryModal{{ $categorie->idCatCl }}"
                                            tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirmation de suppression</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Êtes-vous sûr de vouloir supprimer cette catégorie client ?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Annuler</button>
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
                    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Ajouter Catégorie Client</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form action="{{ route('categorieclient.ajouter') }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul class="mb-0">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        <div class="mb-2">
                                            <label for="codeCatCl">Code</label>
                                            <input type="text" class="form-control" name="codeCatCl" required>
                                        </div>
                                        <div>
                                            <label for="libelle">Libellé</label>
                                            <input type="text" class="form-control" name="libelle" required>
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
    </div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
        @if ($errors->any())
            myModal.show();
        @endif
    });
</script>
