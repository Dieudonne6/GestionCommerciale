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
                            <h4 class="card-title">Liste des factures Pro Formats</h4><br>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBoaModal">
                                <i class="fa-solid fa-plus me-1"></i> Créer une facture pro format
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-0">
                    <div class="table-responsive">
                        
                        <table class="table datatable" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th>No Facture</th>
                                    <th>Client</th>
                                    <th>Date opération</th>
                                    <th>Montant total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>VNT-001</td>
                                    <td>Client Test</td>
                                    <td>2026-01-27</td>
                                    <td>150 000</td>
                                    <td>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBoardModal1">
                                            Supprimer
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal ajout vente -->
    <div class="modal fade" id="addBoaModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Créer une facture pro format</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Numéro Facture</label>
                            <input type="text" class="form-control" value="NFPF-002" readonly>
                        </div>
                        <div class="col-md-6">
                            <label>Date </label>
                            <input type="datetime-local" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Nom Client</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Contact Client</label>
                            <input type="text" class="form-control">
                        </div>                       
                    </div>
                    <div class="d-flex justify-content-between align-items-end mb-2">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="ajouterLigne()">
                            + Ajouter une ligne
                        </button>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Article</th>
                                <th>Qté</th>
                                <th>PU</th>
                                <th>HT</th>
                                <th>TTC</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                      <tbody id="lignesFacture">

                            <tr>
                                <td>
                                    <select class="form-select">
                                        <option>Produit A</option>
                                        <option>Produit B</option>
                                    </select>
                                </td>
                                <td><input type="number" class="form-control"></td>
                                <td><input type="number" class="form-control" readonly></td>
                                <td><input type="number" class="form-control" readonly></td>
                                <td><input type="number" class="form-control"></td>
                                <td>
                                    <button class="btn btn-danger">X</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="row">
                        <div class="col-md-6">
                            <label>Total HT</label>
                            <input type="text" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label>Total TTC</label>
                            <input type="text" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <button class="btn btn-primary">Enregistrer</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    let index = 1;

    function ajouterLigne() {
        const tbody = document.getElementById('lignesFacture');

        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <select class="form-select">
                    <option>Produit A</option>
                    <option>Produit B</option>
                </select>
            </td>
            <td><input type="number" class="form-control"></td>
            <td><input type="number" class="form-control" readonly></td>
            <td><input type="number" class="form-control" readonly></td>
            <td><input type="number" class="form-control"></td>
            <td>
                <button type="button" class="btn btn-danger" onclick="this.closest('tr').remove()">X</button>
            </td>
        `;
        tbody.appendChild(row);
        index++;
    }
</script>

@endsection
