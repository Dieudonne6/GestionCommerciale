@extends('layouts.master')
@section('content')
<div class="page-wrapper">
  
  <!-- Page Content-->
  <div class="page-content">
    <div class="container-xxl"> 
        <h1>Gestion des Categories</h1>
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div class="row align-items-center">
                <div class="col">                      
                  <h4 class="card-title">Categories</h4>                      
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
                      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal"><i class="fa-solid fa-plus me-1"></i> Ajouter une catégorie</button>
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
                      <th class="text-center">Code</th>
                      <th class="ps-0 text-center">Nom Catégorie</th>
                      <th class="text-center">Image</th>
                      <th class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="text-center">
                      <td style="width: 16px;">
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input" name="check" id="customCheck1">
                        </div>
                      </td>
                      <td>1</td>
                      <td>Sports</td>
                      <td class="ps-0">
                        <img src="assets/images/products/04.png" alt="" height="40">
                      </td>
                      <td>                                                       
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
    
    <!-- Modal for adding category -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addCategoryModalLabel">Ajouter une catégorie</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data">
              @csrf
              <div class="mb-3">
                <label for="categoryName" class="form-label">Nom de la catégorie</label>
                <input type="text" class="form-control" id="categoryName" name="categoryName" required>
              </div>
              <div class="mb-3">
                <label for="categoryImage" class="form-label">Image</label>
                <input type="file" class="form-control" id="categoryImage" name="categoryImage" accept="image/*" required>
              </div>
              <div class="text-center">
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

      .modal-content {
        border-radius: 8px;
      }
    </style>
    @endsection
