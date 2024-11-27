@extends('layouts.master')
@section('content')

<div class="row">
    <div class="card" style="width: 90%; margin-left: 30px;">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col-12">                      
            <h4 class="card-title">Réception</h4>
          </div><!--end col-->
        </div><!--end row-->
      </div><!--end card-header-->
        <div class="card-body pt-0">
            <div class="d-flex flex-wrap justify-content-between">
                <div class="d-flex flex-column mx-4" style="flex: 1; min-width: 200px;">
                    <label for="typeC">Commande</label>
                    <input type="text" name="typeC" id="typeC" class="form-control" style="width: 100%;">
                </div>
                <div class="d-flex flex-column mx-4" style="flex: 1; min-width: 200px;">
                    <label for="dateC">Date</label>
                    <input type="date" name="dateC" id="dateC" class="form-control" style="width: 100%;">
                </div>
            </div>
            <br>
            <div class="d-flex flex-wrap justify-content-between">
                <div class="d-flex flex-column mx-4" style="flex: 1; min-width: 200px;">
                    <label for="referenceC">Référence BL</label>
                    <input type="text" name="referenceC" id="referenceC" class="form-control" style="width: 100%;">
                </div>
                <div class="d-flex flex-column mx-4" style="flex: 1; min-width: 200px;">
                    <label for="magasinC">Magasin</label>
                    <select class="form-control"></select>    
                </div>
            </div>
            <button class="btn btn-primary justify-content-end" style="margin-left: 950px; margin-top: 20px;">Générer</button>
            <hr>
            <h5>Détails sur l'entrée de l'article</h5>
            <table class="table table-bordered table-striped ">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Quantité</th>
                        <th>Prix Unitaire</th>
                        <th>Quantité restante</th>
                        <th>Montant TTC</th>    
                    </tr>
                </thead>
            </table>
        </div>
    </div><!--end card-->
  </div><!--end row-->
@endsection
