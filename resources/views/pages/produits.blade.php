@extends('layouts.master')
@section('content')
    <div class="container"> 
      @if(session('success'))
      <div class="alert alert-success">
        {{ session('success') }}
        </div>
      @endif
      <div class="row">
          <div class="card" style="margin-bottom: 150px; margin-right: 50px;">
            <div class="card-header">
              <div class="row align-items-center">
                <div class="col">                      
                  <h4 class="card-title">Liste des produits</h4>                      
                </div><!--end col-->
                <div class="col-auto"> 
                  <form class="row g-2">
                    <div class="col-auto">
                      <button type="button" class="btn btn-primary"  data-bs-toggle="modal" data-bs-target="#addBoard"><i class="fa-solid fa-plus me-1"></i> Ajouter un produit</button>
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
                        <input type="checkbox" class="form-check-input" name="select-all" id="select-all">                                                    
                      </div>
                    </th>
                    <th class="ps-0 text-center">Nom du produit</th>
                    <th class="text-center">Description</th>
                    <th class="text-center">Image</th>
                    <th class="text-center">Quantité</th>
                    <th class="text-center">Catégorie</th>
                    <th class="text-center">Prix de vente</th>
                    <th class="text-center">Stockdown</th>
                    <th class="text-center">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($produits as $produit)
                  <tr>
                    <td><input type="checkbox" class="form-check-input" name="check" id="customCheck1"></td>
                    <td>{{ $produit->NomP }}</td>
                    <td>{{ $produit->descP }}</td>
                    <td class="ps-0" style="width: auto;0 height: auto;">
                      <img src="{{ asset('storage/' . $produit->imgP) }}"
                          alt="{{ $produit->NomP }}" height="40">
                    </td>
                    <td>{{ $produit->qteP }}</td>
                    <td>{{ $produit->categorieP }}</td>
                    <td>{{ $produit->PrixVente }} FCFA</td>
                    <td>{{ $produit->stockDown }}</td>
                    <td>
                      <div class="d-flex justify-content-center">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editBoard{{ $produit->idP }}">Modifier</button>
                        <button style="margin-left: 10px;" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBoard{{ $produit->idP }}">Supprimer</button>
                      </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
              </div>
            </div>
          </div>
      </div> <!-- end row -->                                     
    </div><!-- container -->

    
    <div data-modal="addBoard" class="modal fade" tabindex="-1" role="dialog" id="addBoard" aria-labelledby="addBoardLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addBoardLabel">Ajouter un produit</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="{{ route('produits.store') }}" enctype="multipart/form-data" method="POST">
              @csrf
              @method('POST')
              <div class="form-group">
                <label for="nom">Nom du produit</label>
                <input type="text" name="nom" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="description">Description</label>
                <input type="text" name="description" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="image">Image</label>
                <input type="file" name="imageproduit" class="form-control" accept="image/*" required>
              </div>
              <div class="form-group">
                <label for="quantite">Quantité</label>
                <input type="number" name="quantite" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="categorie">Catégorie</label>
                <select name="categorie" class="form-control">
                  @foreach ($categories as $categorie)
                    <option value="{{ $categorie->id }}" {{ $categorie->NomC == $categorie->id ? 'selected' : '' }}>{{ $categorie->nom }}</option>
                  @endforeach
                  <option value="0">Aucune</option>
                </select>
              </div>
              <div class="form-group">
                <label for="prix">Prix</label>
                <input type="number" name="prix" class="form-control" required>
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

    @foreach ($produits as $produit)
<div class="modal fade" id="editBoard{{ $produit->idP }}" tabindex="-1" role="dialog" aria-labelledby="editBoardLabel{{ $produit->idP }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBoardLabel{{ $produit->idP }}">Modifier un produit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('produits.update', ['idP' => $produit->idP]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="nom">Nom du produit</label>
                        <input type="text" name="nom" class="form-control" value="{{ $produit->NomP }}" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" name="description" class="form-control" value="{{ $produit->descP }}" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" name="image" class="form-control" value="{{ $produit->imgP }}">
                    </div>
                    <div class="form-group">
                        <label for="quantite">Quantité</label>
                        <input type="number" name="quantite" class="form-control" value="{{ $produit->qteP }}" required>
                    </div>
                    <div class="form-group">
                        <label for="categorie">Catégorie</label>
                        <select name="categorie" class="form-control" required>
                            @foreach ($categories as $categorie)
                                <option value="{{ $categorie->id }}" {{ $produit->categorieP == $categorie->id ? 'selected' : '' }}>{{ $categorie->nom }}</option>
                            @endforeach
                            <option value="0" {{ $produit->categorieP == 0 ? 'selected' : '' }}>Aucune</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="prix">Prix</label>
                        <input type="number" name="prix" class="form-control" value="{{ $produit->PrixVente }}" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteBoard{{ $produit->idP }}" tabindex="-1" role="dialog" aria-labelledby="deleteBoardLabel{{ $produit->idP }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteBoardLabel{{ $produit->idP }}">Supprimer un produit</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Voulez-vous vraiment supprimer ce produit ?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            <form action="{{ route('produits.destroy', ['idP' => $produit->idP]) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Supprimer</button>
            </form>
          </div>
        </div>
    </div>
</div>
@endforeach
    @endsection

    @section('styles')
    <style>
          #datatable_1 td, #datatable_1 th {
    text-align: center;
  }
  /* Centrer les images dans les cellules */
  #datatable_1 td img {
    display: block;
    margin: 0 auto;
    height: auto;
  }

  /* Centrer le texte dans les liens aussi */
  #datatable_1 td a {
    display: inline-block;
    text-align: center;
  }
      </style>
    @endsection

    
    