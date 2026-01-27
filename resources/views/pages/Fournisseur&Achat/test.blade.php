@extends('layouts.master')

@section('content')
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            @page {
                size: A5;
                margin: 10mm;
            }

            body {
                font-size: 12px;
            }

            .card {
                border: none;
            }
        }
    </style>

    <div class="container-xxl">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header text-center position-relative">
                        <h4 class="mb-0">
                            <strong>Facture Pro Forma N° NFPF-002</strong>
                        </h4>
                        <button class="btn btn-secondary btn-sm no-print position-absolute end-0 top-50 translate-middle-y me-2"
                                onclick="window.print()">
                            Imprimer
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3 align-items-center">
                            <div class="col-3">
                                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="max-width:80px;">
                            </div>
                            <div class="col-9 text-end">
                                <strong>MA BOUTIQUE</strong><br>
                                Email : boutique@email.com<br>
                                Tél : +229 90 00 00 00<br>
                                Adresse : Cotonou, Bénin
                            </div>
                        </div>

                        <hr class="my-2">
                        
                        <div class="mb-1 text-end">
                            <strong>{{ now()->format('d/m/Y H:i') }}</strong>
                        </div>

                        <!-- Infos facture -->
                        <div class="row mb-3">
                            <div class="col-md-6 ">
                                <p> <strong>Client :</strong> Client Test</p>
                                <p> <strong>Contact :</strong> 01 23 45 67</p>
                            </div>
                        </div>

                        <!-- Tableau produits -->
                        <table class="table table-bordered">
                            
                            <thead class="table-light">
                                <tr>
                                    <th>Article</th>
                                    <th class="text-center">Qté</th>
                                    <th class="text-end">Prix Unitaire</th>
                                    <th class="text-end">Montant HT</th>
                                    <th class="text-end">Montant TTC</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Produit A</td>
                                    <td class="text-center">2</td>
                                    <td class="text-end">10 000</td>
                                    <td class="text-end">20 000</td>
                                    <td class="text-end">23 600</td>
                                </tr>
                                <tr>
                                    <td>Produit B</td>
                                    <td class="text-center">1</td>
                                    <td class="text-end">50 000</td>
                                    <td class="text-end">50 000</td>
                                    <td class="text-end">59 000</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Totaux -->
                        <div class="row justify-content-end mt-3">
                            <div class="col-md-4">
                                <table class="table">
                                    <tr>
                                        <th>Total HT</th>
                                        <td class="text-end">70 000</td>
                                    </tr>
                                    <tr>
                                        <th>Total TVA</th>
                                        <td class="text-end">70 000</td>
                                    </tr>
                                    <tr>
                                        <th>Total TTC</th>
                                        <td class="text-end fw-bold">82 600</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Mention -->
                        <div class="mt-4 text-center text-muted">
                            <em>Cette facture est une facture pro forma et ne constitue pas une facture définitive.</em>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
