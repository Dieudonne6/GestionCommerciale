@extends('layouts.master')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Utilisateurs</h4>
                            </div><!--end col-->
                            <div class="col-auto">
                                <form class="row g-2">
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal"><i class="fa-solid fa-plus me-1"></i> Add
                                            Utilisateurs</button>
                                    </div><!--end col-->

                                    <!-- Modal -->
                                    <div class="modal fade" id="exampleModal" tabindex="-1"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    ...
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary">Save changes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
                                        <th class="ps-0">Customer</th>
                                        <th>Email</th>
                                        <th>Phone No</th>
                                        <th>Status</th>
                                        <th>Rôle</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><a href="ecommerce-order-details.html">#546987</a></td>
                                        <td>
                                            <p class="d-inline-block align-middle mb-0">
                                                <span class="d-block align-middle mb-0 product-name text-body">Bata Shoes</span>
                                                <span class="text-muted font-13">size-08 (Model 2024)</span> 
                                            </p>
                                        </td>
                                        <td>15/08/2023</td>
                                        <td>UPI</td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success"><i class="fas fa-check me-1"></i> Completed</span>
                                        </td>
                                        <td>$390</td>
                                        <td class="text-end">                                                       
                                            <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                            <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><a href="ecommerce-order-details.html">#362514</a></td>
                                        <td>
                                            <p class="d-inline-block align-middle mb-0">
                                                <span class="d-block align-middle mb-0 product-name text-body">Morden Chair</span>
                                                <span class="text-muted font-13">Size-Mediam (Model 2021)</span> 
                                            </p>
                                        </td>
                                        <td>22/09/2023</td>
                                        <td>Banking</td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success"><i class="fas fa-check me-1"></i> Completed</span>
                                        </td>
                                        <td>$630</td>
                                        <td class="text-end">                                                       
                                            <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                            <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><a href="ecommerce-order-details.html">#215487</a></td>
                                        <td>
                                            <p class="d-inline-block align-middle mb-0">
                                                <span class="d-block align-middle mb-0 product-name text-body">Reebok Shoes</span>
                                                <span class="text-muted font-13">size-08 (Model 2021)</span> 
                                            </p>
                                        </td>
                                        <td>31/12/2023</td>
                                        <td>Paypal</td>
                                        <td>
                                            <span class="badge bg-danger-subtle text-danger"><i class="fas fa-xmark me-1"></i> Cancle</span>
                                        </td>
                                        <td>$450</td>
                                        <td class="text-end">                                                       
                                            <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                            <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><a href="ecommerce-order-details.html">#326598</a></td>
                                        <td>
                                            <p class="d-inline-block align-middle mb-0">
                                                <span class="d-block align-middle mb-0 product-name text-body">Cosco Vollyboll</span>
                                                <span class="text-muted font-13">size-04 (Model 2021)</span> 
                                            </p>
                                        </td>
                                        <td>05/01/2024</td>
                                        <td>UPI</td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success"><i class="fas fa-check me-1"></i> Completed</span>
                                        </td>
                                        <td>$880</td>
                                        <td class="text-end">                                                       
                                            <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                            <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                        </td>
                                    </tr>                                                                                 
                                    <tr>
                                        <td><a href="ecommerce-order-details.html">#369852</a></td>
                                        <td>
                                            <p class="d-inline-block align-middle mb-0">
                                                <span class="d-block align-middle mb-0 product-name text-body">Royal Purse</span>
                                                <span class="text-muted font-13">Pure Lether 100%</span> 
                                            </p>
                                        </td>
                                        <td>20/02/2024</td>
                                        <td>BTC</td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary"><i class="fas fa-clock me-1"></i> Pendding</span>
                                        </td>
                                        <td>$520</td>
                                        <td class="text-end">                                                       
                                            <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                            <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><a href="ecommerce-order-details.html">#987456</a></td>
                                        <td>
                                            <p class="d-inline-block align-middle mb-0">
                                                <span class="d-block align-middle mb-0 product-name text-body">Bata Shoes</span>
                                                <span class="text-muted font-13">size-08 (Model 2024)</span> 
                                            </p>
                                        </td>
                                        <td>15/08/2023</td>
                                        <td>UPI</td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success"><i class="fas fa-check me-1"></i> Completed</span>
                                        </td>
                                        <td>$390</td>
                                        <td class="text-end">                                                       
                                            <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                            <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><a href="ecommerce-order-details.html">#159753</a></td>
                                        <td>
                                            <p class="d-inline-block align-middle mb-0">
                                                <span class="d-block align-middle mb-0 product-name text-body">Morden Chair</span>
                                                <span class="text-muted font-13">Size-Mediam (Model 2021)</span> 
                                            </p>
                                        </td>
                                        <td>22/09/2023</td>
                                        <td>Banking</td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success"><i class="fas fa-check me-1"></i> Completed</span>
                                        </td>
                                        <td>$630</td>
                                        <td class="text-end">                                                       
                                            <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                            <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><a href="ecommerce-order-details.html">#852456</a></td>
                                        <td>
                                            <p class="d-inline-block align-middle mb-0">
                                                <span class="d-block align-middle mb-0 product-name text-body">Reebok Shoes</span>
                                                <span class="text-muted font-13">size-08 (Model 2021)</span> 
                                            </p>
                                        </td>
                                        <td>31/12/2023</td>
                                        <td>Paypal</td>
                                        <td>
                                            <span class="badge bg-danger-subtle text-danger"><i class="fas fa-xmark me-1"></i> Cancle</span>
                                        </td>
                                        <td>$450</td>
                                        <td class="text-end">                                                       
                                            <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                            <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><a href="ecommerce-order-details.html">#154863</a></td>
                                        <td>
                                            <p class="d-inline-block align-middle mb-0">
                                                <span class="d-block align-middle mb-0 product-name text-body">Cosco Vollyboll</span>
                                                <span class="text-muted font-13">size-04 (Model 2021)</span> 
                                            </p>
                                        </td>
                                        <td>05/01/2024</td>
                                        <td>UPI</td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success"><i class="fas fa-check me-1"></i> Completed</span>
                                        </td>
                                        <td>$880</td>
                                        <td class="text-end">                                                       
                                            <a href="#"><i class="las la-pen text-secondary fs-18"></i></a>
                                            <a href="#"><i class="las la-trash-alt text-secondary fs-18"></i></a>
                                        </td>
                                    </tr>                                                                                 
                                    <tr>
                                        <td><a href="ecommerce-order-details.html">#625877</a></td>
                                        <td>
                                            <p class="d-inline-block align-middle mb-0">
                                                <span class="d-block align-middle mb-0 product-name text-body">Royal Purse</span>
                                                <span class="text-muted font-13">Pure Lether 100%</span> 
                                            </p>
                                        </td>
                                        <td>20/02/2024</td>
                                        <td>BTC</td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary"><i class="fas fa-clock me-1"></i> Pendding</span>
                                        </td>
                                        <td>$520</td>
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
</div @endsection
