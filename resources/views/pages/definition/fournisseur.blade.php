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
  
  <div class="row justify-content-center">
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
                  <th class="ps-0">Nom et prénoms</th>
                  <th>Adresse</th>
                  <th>Contact</th>
                  <th class="text-end">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="ps-0">
                    <p class="d-inline-block align-middle mb-0">
                      <span class="font-13 fw-medium">Andy Timmons</span>
                    </p>
                  </td>
                  <td>koko</td>
                  <td>(+1) 123 456 789</td>
                  <td class="text-end">
                    <button class="btn btn-primary">Modifier</button>
                    <button class="btn btn-danger">Supprimer</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div> 
        <div class="modal fade" id="addBoardModal" tabindex="-1" aria-labelledby="addBoardModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Ajouter un Fournisseur</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="mb-2">
                  <input type="text" class="form-control"  placeholder="Nom">
                </div>
                <div class="mb-2">
                  <input type="text" class="form-control"  placeholder="Prenom">
                </div>
                <div class="mb-2">
                  <input type="text" class="form-control"  placeholder="Adresse">
                </div>
                <div>
                  <input type="text" class="form-control"  placeholder="Contact">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary">Envoyer</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  @endsection