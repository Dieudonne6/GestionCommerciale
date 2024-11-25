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
                      <th class="ps-0">Nom du Catégorie</th>
                      <th>Pics</th>
                      <th>Créé le</th>
                      <th class="text-end">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td style="width: 16px;">
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input" name="check"  id="customCheck1">
                        </div>
                      </td>
                      <td class="ps-0">
                        <img src="assets/images/products/04.png" alt="" height="40">
                        <p class="d-inline-block align-middle mb-0">
                          <a href="ecommerce-order-details.html" class="d-inline-block align-middle mb-0 product-name">Apple Watch</a> 
                          <br>
                          <span class="text-muted font-13">Size-05 (Model 2021)</span> 
                        </p>
                      </td>
                      <td>Sports</td>
                      <td>32</td>
                      <td>$39</td>
                      <td><span class="badge bg-success-subtle text-success"><i class="fas fa-check me-1"></i> Published</span></td>
                      <td>
                        <span>14 Jan 2024, 10:30am</span>
                      </td>
                      <td class="text-end">                                                       
                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                      </td>
                    </tr>
                    <tr>
                      <td style="width: 16px;">
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input" name="check"  id="customCheck2">
                        </div>
                      </td>
                      <td class="ps-0">
                        <img src="assets/images/products/01.png" alt="" height="40">
                        <p class="d-inline-block align-middle mb-0">
                          <a href="ecommerce-order-details.html" class="d-inline-block align-middle mb-0 product-name">Morden Chair</a> 
                          <br>
                          <span class="text-muted font-13">Size-Mediam (Model 2021)</span> 
                        </p>
                      </td>
                      <td>Interior</td>
                      <td>10</td>
                      <td>$99</td>
                      <td><span class="badge bg-success-subtle text-success"><i class="fas fa-check me-1"></i> Published</span></td>
                      <td>
                        <span>18 Jan 2024, 12:10am</span>
                      </td>
                      <td class="text-end">                                                       
                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                      </td>
                    </tr>
                    <tr>
                      <td style="width: 16px;">
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input" name="check"  id="customCheck3">
                        </div>
                      </td>
                      <td class="ps-0">
                        <img src="assets/images/products/05.png" alt="" height="40">
                        <p class="d-inline-block align-middle mb-0">
                          <a href="ecommerce-order-details.html" class="d-inline-block align-middle mb-0 product-name">Reebok Shoes</a> 
                          <br>
                          <span class="text-muted font-13">size-08 (Model 2021)</span> 
                        </p>
                      </td>
                      <td>Footwear</td>
                      <td>24</td>
                      <td>$49</td>
                      <td><span class="badge bg-danger-subtle text-danger"><i class="fas fa-xmark me-1"></i> Inactive</span></td>
                      <td>
                        <span>24 Jan 2024, 09:10pm</span>
                      </td>
                      <td class="text-end">                                                       
                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                      </td>
                    </tr>
                    <tr>
                      <td style="width: 16px;">
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input" name="check"  id="customCheck4">
                        </div>
                      </td>
                      <td class="ps-0">
                        <img src="assets/images/products/06.png" alt="" height="40">
                        <p class="d-inline-block align-middle mb-0">
                          <a href="ecommerce-order-details.html" class="d-inline-block align-middle mb-0 product-name">Cosco Vollyboll</a> 
                          <br>
                          <span class="text-muted font-13">size-04 (Model 2021)</span> 
                        </p>
                      </td>
                      <td>Sports</td>
                      <td>8</td>
                      <td>$49</td>
                      <td><span class="badge bg-success-subtle text-success"><i class="fas fa-check me-1"></i> Published</span></td>
                      <td>
                        <span>08 Feb 2024, 03:30pm</span>
                      </td>
                      <td class="text-end">                                                       
                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                      </td>
                    </tr>
                    <tr>
                      <td style="width: 16px;">
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input" name="check"  id="customCheck5">
                        </div>
                      </td>
                      <td class="ps-0">
                        <img src="assets/images/products/04.png" alt="" height="40">
                        <p class="d-inline-block align-middle mb-0">
                          <a href="ecommerce-order-details.html" class="d-inline-block align-middle mb-0 product-name">Royal Purse</a> 
                          <br>
                          <span class="text-muted font-13">Pure Lether 100%</span> 
                        </p>
                      </td>
                      <td>Life Style</td>
                      <td>52</td>
                      <td>$89</td>
                      <td><span class="badge bg-success-subtle text-success"><i class="fas fa-check me-1"></i> Published</span></td>
                      <td>
                        <span>20 Feb 2024, 11:30am</span>
                      </td>
                      <td class="text-end">                                                       
                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                      </td>
                    </tr>
                    <tr>
                      <td style="width: 16px;">
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input" name="check"  id="customCheck6">
                        </div>
                      </td>
                      <td class="ps-0">
                        <img src="assets/images/products/03.png" alt="" height="40">
                        <p class="d-inline-block align-middle mb-0">
                          <a href="ecommerce-order-details.html" class="d-inline-block align-middle mb-0 product-name">New Morden Chair</a> 
                          <br>
                          <span class="text-muted font-13">size-05 (Model 2021)</span> 
                        </p>
                      </td>
                      <td>Interior</td>
                      <td>6</td>
                      <td>$20</td>
                      <td><span class="badge bg-secondary-subtle text-secondary"><i class="fas fa-box-archive me-1"></i> Draft</span></td>
                      <td>
                        <span>14 Jan 2024, 10:30am</span>
                      </td>
                      <td class="text-end">                                                       
                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                      </td>
                    </tr>
                    <tr>
                      <td style="width: 16px;">
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input" name="check"  id="customCheck7">
                        </div>
                      </td>
                      <td class="ps-0">
                        <img src="assets/images/products/02.png" alt="" height="40">
                        <p class="d-inline-block align-middle mb-0">
                          <a href="ecommerce-order-details.html" class="d-inline-block align-middle mb-0 product-name">Important Chair</a> 
                          <br>
                          <span class="text-muted font-13">size-05 (Model 2021)</span> 
                        </p>
                      </td>
                      <td>Interior</td>
                      <td>32</td>
                      <td>$39</td>
                      <td><span class="badge bg-success-subtle text-success"><i class="fas fa-check me-1"></i> Published</span></td>
                      <td>
                        <span>02 Mar 2024, 10:30am</span>
                      </td>
                      <td class="text-end">                                                       
                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                      </td>
                    </tr>
                    <tr>
                      <td style="width: 16px;">
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input" name="check"  id="customCheck8">
                        </div>
                      </td>
                      <td class="ps-0">
                        <img src="assets/images/products/02.png" alt="" height="40">
                        <p class="d-inline-block align-middle mb-0">
                          <a href="ecommerce-order-details.html" class="d-inline-block align-middle mb-0 product-name">Nivya Footboll</a> 
                          <br>
                          <span class="text-muted font-13">Size-05 (Model 2021)</span> 
                        </p>
                      </td>
                      <td>Sports</td>
                      <td>32</td>
                      <td>$39</td>
                      <td><span class="badge bg-danger-subtle text-danger"><i class="fas fa-xmark me-1"></i> Inactive</span></td>
                      <td>
                        <span>11 Mar 2024, 05:30am</span>
                      </td>
                      <td class="text-end">                                                       
                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                      </td>
                    </tr>
                    <tr>
                      <td style="width: 16px;">
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input" name="check"  id="customCheck9">
                        </div>
                      </td>
                      <td class="ps-0">
                        <img src="assets/images/products/01.png" alt="" height="40">
                        <p class="d-inline-block align-middle mb-0">
                          <a href="ecommerce-order-details.html" class="d-inline-block align-middle mb-0 product-name">Green Morden Chair</a> 
                          <br>
                          <span class="text-muted font-13">Size-Mediam (Model 2021)</span> 
                        </p>
                      </td>
                      <td>Interior</td>
                      <td>10</td>
                      <td>$99</td>
                      <td><span class="badge bg-success-subtle text-success"><i class="fas fa-check me-1"></i> Published</span></td>
                      <td>
                        <span>28 Mar 2024, 08:45am</span>
                      </td>
                      <td class="text-end">                                                       
                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                      </td>
                    </tr>
                    <tr>
                      <td style="width: 16px;">
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input" name="check"  id="customCheck10">
                        </div>
                      </td>
                      <td class="ps-0">
                        <img src="assets/images/products/01.png" alt="" height="40">
                        <p class="d-inline-block align-middle mb-0">
                          <a href="ecommerce-order-details.html" class="d-inline-block align-middle mb-0 product-name">Bata Shoes</a> 
                          <br>
                          <span class="text-muted font-13">size-08 (Model 2021)</span> 
                        </p>
                      </td>
                      <td>Footwear</td>
                      <td>24</td>
                      <td>$49</td>
                      <td><span class="badge bg-secondary-subtle text-secondary"><i class="fas fa-box-archive me-1"></i> Draft</span></td>
                      <td>
                        <span>05 Apr 2024, 12:30am</span>
                      </td>
                      <td class="text-end">                                                       
                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                      </td>
                    </tr>
                    <tr>
                      <td style="width: 16px;">
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input" name="check"  id="customCheck11">
                        </div>
                      </td>
                      <td class="ps-0">
                        <img src="assets/images/products/06.png" alt="" height="40">
                        <p class="d-inline-block align-middle mb-0">
                          <a href="ecommerce-order-details.html" class="d-inline-block align-middle mb-0 product-name">Cosco Vollyboll</a> 
                          <br>
                          <span class="text-muted font-13">size-04 (Model 2021)</span> 
                        </p>
                      </td>
                      <td>Sports</td>
                      <td>8</td>
                      <td>$49</td>
                      <td><span class="badge bg-success-subtle text-success"><i class="fas fa-check me-1"></i> Published</span></td>
                      <td>
                        <span>10 Apr 2024, 10:30am</span>
                      </td>
                      <td class="text-end">                                                       
                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                      </td>
                    </tr>
                    <tr>
                      <td style="width: 16px;">
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input" name="check"  id="customCheck12">
                        </div>
                      </td>
                      <td class="ps-0">
                        <img src="assets/images/products/04.png" alt="" height="40">
                        <p class="d-inline-block align-middle mb-0">
                          <a href="ecommerce-order-details.html" class="d-inline-block align-middle mb-0 product-name">Royal Purse</a> 
                          <br>
                          <span class="text-muted font-13">Pure Lether 100%</span> 
                        </p>
                      </td>
                      <td>Life Style</td>
                      <td>52</td>
                      <td>$89</td>
                      <td><span class="badge bg-success-subtle text-success"><i class="fas fa-check me-1"></i> Published</span></td>
                      <td>
                        <span>20 Apr 2024, 10:30am</span>
                      </td>
                      <td class="text-end">                                                       
                        <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                        <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                      </td>
                    </tr>
                    <tr>
                      <td style="width: 16px;">
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input" name="check"  id="customCheck13">
                        </div>
                      </td>
                      <td class="ps-0">
                        <img src="assets/images/products/03.png" alt="" height="40">
                        <p class="d-inline-block align-middle mb-0">
                          <a href="ecommerce-order-details.html" class="d-inline-block align-middle mb-0 product-name">New Morden Chair</a> 
                          <br>
                          <span class="text-muted font-13">size-05 (Model 2021)</span> 
                        </p>
                      </td>
                      <td>Interior</td>
                      <td>6</td>
                      <td>$20</td>
                      <td><span class="badge bg-success-subtle text-success"><i class="fas fa-check me-1"></i> Published</span></td>
                      <td>
                        <span>08 May 2024, 01:30am</span>
                      </td>
                      <td class="text-end">                                                       
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
            <h5 class="modal-title" id="addBoardLabel">Ajouter une categorie</h5>
          </div>
        </div>
      </div>
    </div>
    @endsection
    
    