@extends('layouts.master')
@section('content')
<div class="page-wrapper">
  
  <!-- Page Content-->
  <div class="page-content">
    <div class="container-xxl"> 
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div class="row align-items-center">
                <div class="col">                      
                  <h4 class="card-title">Produits</h4>                      
                </div><!--end col-->
                <div class="col-auto"> 
                  <form class="row g-2">
                    <div class="col-auto">
                      <a class="btn bg-primary-subtle text-primary dropdown-toggle d-flex align-items-center arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false" data-bs-auto-close="outside">
                        <i class="iconoir-filter-alt me-1"></i> Filtrer
                      </a>
                      <div class="dropdown-menu dropdown-menu-start">
                        <div class="p-2">
                          <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" checked id="filter-all">
                            <label class="form-check-label" for="filter-all">
                              Tous
                            </label>
                          </div>
                          <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" checked id="filter-one">
                            <label class="form-check-label" for="filter-one">
                              Mode
                            </label>
                          </div>
                          <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" checked id="filter-two">
                            <label class="form-check-label" for="filter-two">
                              Plante
                            </label>
                          </div>
                          <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" checked id="filter-three">
                            <label class="form-check-label" for="filter-three">
                              Jouet
                            </label>
                          </div>
                          <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" checked id="filter-four">
                            <label class="form-check-label" for="filter-four">
                              Gadget
                            </label>
                          </div>
                          <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" checked id="filter-five">
                            <label class="form-check-label" for="filter-five">
                              Aliment
                            </label>
                          </div>
                          <div class="form-check">
                            <input type="checkbox" class="form-check-input" checked id="filter-six">
                            <label class="form-check-label" for="filter-six">
                              Boisson
                            </label>
                          </div>
                        </div>
                      </div>
                    </div><!--end col-->
                    
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
      <th class="text-center">Code du Produit</th>
      <th class="ps-0 text-center">Nom du produit</th>
      <th class="text-center">Description</th>
      <th class="text-center">Image</th>
      <th class="text-center">Quantite</th>
      <th class="text-center">Catégorie</th>
      <th class="text-center">Prix de vente</th>
      <th class="text-center">Stockdown</th>
      <th class="text-center">Action</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td style="width: 16px;">
        <div class="form-check">
          <input type="checkbox" class="form-check-input" name="check" id="customCheck1">
        </div>
      </td>
      <td class="ps-0 text-center">
        <span>1</span>
      </td>
      <td class="text-center">
        <a href="ecommerce-order-details.html" class="d-inline-block align-middle mb-0 product-name">Apple Watch</a>
      </td>
      <td class="text-center">Lorem ipsum dolor sit amet.</td>
      <td class="text-center">
        <img src="assets/images/products/04.png" alt="" height="40">
      </td>
      <td class="text-center">
        <span>150</span>
      </td>
      <td class="text-center">
        <span>Sports</span>
      </td>
      <td class="text-center">
        <span>$39</span>
      </td>
      <td class="text-center">
      <span class="text-danger">15</span>
      </td>
      <td class="text-center">
        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
      </td>
    </tr>
    <tr>
      <td style="width: 16px;">
        <div class="form-check">
          <input type="checkbox" class="form-check-input" name="check" id="customCheck1">
        </div>
      </td>
      <td class="ps-0 text-center">
        <span>2</span>
      </td>
      <td class="text-center">
        <a href="ecommerce-order-details.html" class="d-inline-block align-middle mb-0 product-name">Morden Chair</a>
      </td>
      <td>Lorem ipsum dolor sit amet.</td>
      <td class="text-center">
        <img src="assets/images/products/01.png" alt="" height="40">
      </td>
      <td class="text-center">
        <span>150</span>
      </td>
      <td class="text-center">
        <span>Interior</span>
      </td>
      <td class="text-center">
        <span>$39</span>
      </td>
      <td class="text-center">
        <span class="text-danger">15</span>
      </td>
      <td class="text-center">
        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
      </td>
    </tr>
  </tbody>
</table>
              </div>
            </div>
          </div>
        </div> <!-- end col -->
      </div> <!-- end row -->                                     
    </div><!-- container -->
    
    <div data-modal="addBoard" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addBoardLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addBoardLabel">Ajouter un produit</h5>
          </div>
        </div>
      </div>
    </div>
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
  }

  /* Centrer le texte dans les liens aussi */
  #datatable_1 td a {
    display: inline-block;
    text-align: center;
  }
      </style>
    @endsection

    
    