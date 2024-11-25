@extends('layouts.master')
@section('content')
    <!-- Page Content-->
    <div class="container-xxl">


        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Liste des commandes d'achat</h4> </br>
                                <button type="button" class="btn btn-primary d-flex">Creer commande</button>

                            </div><!--end col-->
                        </div> <!--end row-->
                    </div><!--end card-header-->
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table datatable" id="datatable_1">
                                <thead class="table-light">
                                    <tr>
                                        <th>No Commande</th>
                                        <th data-type="date" data-format="YYYY/DD/MM">Date Operation</th>
                                        <th data-type="date" data-format="YYYY/DD/MM">Date Remise</th>
                                        <th>Montant HT</th>
                                        {{-- <th>Completion</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>CA 0000000001</td>
                                        <td>2024/12/01</td>
                                        <td>2024/12/02</td>
                                        <td>50 000 Fcfa</td>
                                    </tr>



                                </tbody>
                            </table>
                        </div>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div> <!--end col-->
        </div><!--end row-->


    </div><!-- container -->
    <!--Start Rightbar-->
    <!--Start Rightbar/offcanvas-->

    <!--end Rightbar/offcanvas-->
    <!--end Rightbar-->
    <!--Start Footer-->



    <!--end footer-->
    <!-- end page content -->
@endsection
