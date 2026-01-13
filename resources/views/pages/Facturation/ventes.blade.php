@extends('layouts.master')
@section('content')
    <!-- Page Content-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <div class="container-xxl">


        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Liste des ventes</h4> </br>
                            </div><!--end col-->
                            <div class="col-auto">
                                <div class="col-auto">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addBoaModal"><i class="fa-solid fa-plus me-1"></i> Ajouter une
                                        vente</button>
                                </div><!--end col-->
                            </div><!--end col-->
                        </div> <!--end row-->
                    </div><!--end card-header-->
                    @if (Session::has('status'))
                        <div id="statusAlert" class="alert alert-success">
                            {{ Session::get('status') }}
                        </div>
                    @endif
                    @if (Session::has('erreur'))
                        <div id="statusAlert" class="alert alert-danger">
                            {{ Session::get('erreur') }}
                        </div>
                    @endif
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table datatable" id="datatable_1">
                                <thead class="table-light">
                                    <tr>
                                        <th>No Vente</th>
                                        <th>Client</th>
                                        <th data-type="date" data-format="YYYY/DD/MM">Date Operation</th>
                                        <th>Montant Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($allVente as $vente)
                                        <tr>
                                            <td>{{ $vente->reference }}</td>
                                            <td>{{ $vente->nomClient ?: ($vente->client ? $vente->client->identiteCl : 'Non défini') }}</td>
                                            <td>{{ $vente->dateOperation }}</td>
                                            <td>{{ $vente->montantTotal }}</td>
                                            <td>
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#ModifyBoardModal{{ $vente->idV }}"> Modifier</button>
                                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteBoardModal{{ $vente->idV }}">
                                                    Supprimer</button>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="deleteBoardModal{{ $vente->idV }}" tabindex="-1"
                                            aria-labelledby="deleteBoardModal{{ $vente->idV }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation de
                                                            suppression</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Êtes-vous sûr de vouloir supprimer cette vente?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Annuler</button>
                                                        <form method="POST"
                                                            action="{{ route('Vente.destroy', $vente->idV) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Supprimer</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div> <!--end col-->
        </div><!--end row-->
        @foreach ($allVente as $vente)
            {{-- Modal de modification --}}
            <div class="modal fade" id="ModifyBoardModal{{ $vente->idV }}" tabindex="-1"
                aria-labelledby="ModifyBoardModal{{ $vente->idV }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Modifier une vente</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ url('modifVente/' . $vente->idV) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row mb-3">
                                    <div class="col-md-4 mb-2">
                                        <label for="ifuclient" class="form-label">IFU Client</label>
                                        <input class="form-control mb-3" type="text" name="IFUClient" value="{{ $vente->IFUClient }}">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label for="nomclient" class="form-label">Nom Client</label>
                                        <input class="form-control mb-3" type="text" name="nomClient" value="{{ $vente->nomClient }}">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label for="telclient" class="form-label">Contact Client</label>
                                        <input class="form-control mb-3" type="text" name="telClient" value="{{ $vente->telClient }}">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label for="reference" class="form-label">Numero vente</label>
                                        <input class="form-control mb-3" type="text" name="reference"
                                            value="{{ $vente->reference }}" readonly>
                                    </div>
                                    <div class="mb-2 col-md-4">
                                        <label for="modepaiement" class="form-label">Mode paiement</label>
                                        <select name="idModPaie" class="form-control" id="modepaiement">
                                            <option value="">Sélectionner un mode</option>
                                            @foreach ($modes as $mode)
                                                <option value="{{ $mode->idModPaie }}" {{ $vente->idModPaie == $mode->idModPaie ? 'selected' : '' }}>
                                                    {{ $mode->libelle }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label for="dateoperation" class="form-label">Date de vente</label>
                                        <input class="form-control mb-3" type="datetime-local" name="dateOperation"
                                            value="{{ $vente->dateOperation }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="description" class="form-label">Description Vente</label>
                                        <input type="text" class="form-control" name="description" id="description"
                                            value="{{ $vente->descV }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="form-label">Vente</label>
                                            <button type="button" class="btn btn-secondary my-2 mx-3"
                                                onclick="ajouterVente()">Ajouter une vente</button>
                                        </div>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Article</th>
                                                    <th>Quantité</th>
                                                    <th>Prix Unitaire</th>
                                                    <th class="montant-header">Montant HT</th>
                                                    <th class="ttc-header">Montant TTC</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="ligneProduits">
                                                @foreach ($vente->detailVente as $index => $ligne)
                                                    <tr>
                                                        <input type="hidden" name="lignes[{{ $index }}][idDV]"
                                                            value="{{ $ligne->idDV }}">

                                                        <td>
                                                            <select id="productSelect"
                                                                name="lignes[{{ $index }}][idP]"
                                                                class="form-select product-select2">
                                                                <option value="">Sélectionner un produit</option>
                                                                @foreach ($allproduits as $produit)
                                                                    <option value="{{ $produit->idPro }}"
                                                                        {{ $ligne->idP == $produit->idPro ? 'selected' : '' }}>
                                                                        {{ $produit->libelle }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="lignes[{{ $index }}][qte]"
                                                                class="form-control qte" value="{{ $ligne->quantite }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="lignes[{{ $index }}][prixU]"
                                                                class="form-control prixU" value="{{ $ligne->prixUnitaire }}" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="lignes[{{ $index }}][montantht]"
                                                                class="form-control montantht" value="{{ $ligne->prixUnitaire * $ligne->quantite }}" readonly>
                                                        </td>
                                                        <td class="ttc-cell">
                                                            <input type="number"
                                                                name="lignes[{{ $index }}][montantttc]"
                                                                class="form-control montantttc"
                                                                value="{{ $ligne->montantTotal }}">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger delete-ligne"
                                                                data-id="{{ $ligne->idDV }}">Supprimer</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="form-label">Récapitulatif</label>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label montant-label">Total HT:</label>
                                                <input type="text" id="totalHT" class="form-control" readonly>
                                            </div>
                                            <div class="col-md-6 ttc-section">
                                                <label class="form-label ttc-label">Total TTC:</label>
                                                <input type="text" id="totalTTC" class="form-control" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{-- <div class="modal fade" id="delteBoardModal{{$allcmd->idCmd}}" tabindex="-1" aria-labelledby="delteBoardModal{{$allcmd->idCmd}}" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation de suppression</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer cette commande?
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form method="POST" action="{{ route('commande.destroy', $allcmd->idCmd) }}">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                  </div>
                </div>
              </div>
            </div> --}}
        @endforeach
        
        {{-- Modal d'ajout --}}
        <div class="modal fade" id="addBoaModal" tabindex="-1" aria-labelledby="addBoaModal" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Ajouter une ligne de vente</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('ajouterVente.store') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-4 mb-2">
                                    <label for="ifuclient" class="form-label">IFU Client</label>
                                    <input class="form-control mb-3" type="text" name="IFUClient" value="">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="nomclient" class="form-label">Nom Client</label>
                                    <input class="form-control mb-3" type="text" name="nomClient" value="">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="telclient" class="form-label">Contact Client</label>
                                    <input class="form-control mb-3" type="text" name="telClient" value="">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="reference" class="form-label">Numero vente</label>
                                    <input class="form-control mb-3" type="text" name="reference" id="reference" readonly>
                                </div>
                                <div class="mb-2 col-md-4">
                                    <label for="modepaiement" class="form-label">Mode paiement</label>
                                    <select name="idModPaie" class="form-control" id="modepaiement">
                                        <option value="">Sélectionner un mode</option>
                                        @foreach ($modes as $mode)
                                            <option value="{{ $mode->idModPaie }}" {{ strtolower($mode->libelle) == 'espèces' ? 'selected' : '' }}>
                                                {{ $mode->libelle }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            
                                <div class="col-md-4 mb-2">
                                    <label for="dateoperation" class="form-label">Date de vente</label>
                                    <input class="form-control mb-3" type="datetime-local" name="dateOperation">
                                </div>

                                <div class="col-md-4">
                                    <label for="description" class="form-label">Description Vente</label>
                                    <input type="text" class="form-control" name="description" id="description"
                                        placeholder="">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label">Vente</label>
                                        <button type="button" class="btn btn-secondary my-2 mx-3"
                                            onclick="ajouterVente()">Ajouter une vente</button>
                                    </div>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Article</th>
                                                <th>Quantité</th>
                                                <th>Prix Unitaire</th>
                                                <th class="montant-header">Montant HT</th>
                                                <th class="ttc-header">Montant TTC</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="ligneProduits">
                                            <tr>
                                                <td>
                                                    <select class="form-select product-select2" name="lignes[0][idP]" id="productSelect">
                                                        <option value="">-- Produit --</option>
                                                        @foreach ($allproduits as $produit)
                                                            <option value="{{ $produit->idPro }}">
                                                                {{ $produit->libelle }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="lignes[0][qte]" class="form-control qte">
                                                </td>
                                                <td>
                                                    <input type="number" name="lignes[0][prixU]" class="form-control prixU">
                                                </td>
                                                <td>
                                                    <input type="number" name="lignes[0][montantht]"
                                                        class="form-control montantht" readonly>
                                                </td>
                                                <td class="ttc-cell">
                                                    <input type="number" name="lignes[0][montantttc]"
                                                        class="form-control montantttc">                                                      
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger"
                                                        onclick="supprimerLigne(this)">Supprimer</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label">Récapitulatif</label>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">Total HT:</label>
                                            <input type="text" id="totalHT" class="form-control" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Total TTC:</label>
                                            <input type="text" id="totalTTC" class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



  <script>
        let ligneIndex = 1;
        let isTPS = {{ $regimeEntreprise === 'TPS' ? 'true' : 'false' }}; // Récupérer le régime depuis le contrôleur

        // Auto-generate reference number and initialize Select2
        $(document).ready(function() {
            $.get("{{ url('get-nouvelle-reference') }}", function(data) {
                $('#reference').val(data.reference);
            });

            // Set current date/time
            var now = new Date();
            var dateTimeLocal = now.toISOString().slice(0,16);
            $('input[name="dateOperation"]').val(dateTimeLocal);
            
            // Initialize Select2 and event listeners
            attachEventListeners();
            initializeSelect2();
            
            // Vérifier le régime de l'entreprise (à adapter selon votre logique)
            checkRegimeTPS();
              
            $(".delete-ligne").click(function() {
                let ligneId = $(this).data("id");
                let row = $(this).closest("tr");

                $.ajax({
                    url: "{{ url('deleteLigneVente') }}/" + ligneId,
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            row.remove();
                            calculateTotals();
                        } else {
                            alert("Erreur lors de la suppression !");
                        }
                    },
                    error: function(xhr) {
                        alert("Une erreur s'est produite !");
                    }
                });
            });
        });

        function checkRegimeTPS() {
            // Le régime est déjà défini depuis PHP via la variable isTPS
            updateDisplayForRegime();
        }

        function updateDisplayForRegime() {
            if (isTPS) {
                // Masquer les éléments TTC
                $('.ttc-header').hide();
                $('.ttc-cell').hide();
                $('.ttc-section').hide();
                
                // Changer les libellés
                $('.montant-header').text('Montant total');
                $('.montant-label').text('Montant total:');
                
                // Ajuster la largeur des colonnes
                $('.montant-header').closest('th').attr('colspan', '2');
            } else {
                // Afficher les éléments TTC
                $('.ttc-header').show();
                $('.ttc-cell').show();
                $('.ttc-section').show();
                
                // Restaurer les libellés
                $('.montant-header').text('Montant HT');
                $('.montant-label').text('Total HT:');
                
                // Restaurer la largeur des colonnes
                $('.montant-header').closest('th').removeAttr('colspan');
            }
        }

        function ajouterVente() {
          const ligne = `<tr>
                          <td>
                            <select id="productSelect" name="lignes[${ligneIndex}][idP]" class="form-select product-select2">
                              <option value="">Sélectionner un produit</option>
                              @foreach ($allproduits as $produit)
                                  <option value="{{ $produit->idPro }}">{{ $produit->libelle }}</option>
                              @endforeach
                          </select>
                          </td>
                          <td>
                            <input type="number" name="lignes[${ligneIndex}][qte]"
                            class="form-control qte">
                          </td>
                          <td>
                            <input type="number" name="lignes[${ligneIndex}][prixU]"
                            class="form-control prixU">
                          </td>
                          <td>
                            <input type="number" name="lignes[${ligneIndex}][montantht]"
                            class="form-control montantht" readonly>
                          </td>
                          <td>
                            <input type="number" name="lignes[${ligneIndex}][montantttc]"
                            class="form-control montantttc">
                          </td>
                          <td>
                            <button type="button" class="btn btn-danger"
                            onclick="supprimerLigne(this)">Supprimer</button>
                          </td>
                        </tr>`;
            document.getElementById('ligneProduits').insertAdjacentHTML('beforeend', ligne);
            ligneIndex++;
            attachEventListeners();
            initializeSelect2();
          }
          
          function supprimerLigne(button) {
            button.closest('tr').remove();
            calculateTotals();
          }

          function attachEventListeners() {
            $('.qte, .prixU, .montantttc').off('input').on('input', function() {
                const row = $(this).closest('tr');
                calculateRowTotal(row);
                calculateTotals();
            });

            $('.product-select2').off('change').on('change', function() {
                const productId = $(this).val();
                const row = $(this).closest('tr');
                
                if (productId) {
                    $.get(`/get-produit-info/${productId}`, function(data) {
                        row.find('.prixU').val(data.prix);
                        calculateRowTotal(row);
                        calculateTotals();
                    });
                }
            });
          }

          function initializeSelect2() {
            $('.product-select2').select2({
                placeholder: "Sélectionner un produit",
                allowClear: false,
                width: '100%'
            });
            
            // Re-initialize Select2 when modal is shown
            $('.modal').on('shown.bs.modal', function() {
                $(this).find('.product-select2').select2({
                    placeholder: "Sélectionner un produit",
                    allowClear: false,
                    width: '100%',
                    dropdownParent: $(this)
                });
            });
          }

          function calculateRowTotal(row) {
            const qte = parseFloat(row.find('.qte').val()) || 0;
            const prixU = parseFloat(row.find('.prixU').val()) || 0;
            const montantHT = qte * prixU;
            const montantTTC = isTPS ? montantHT : montantHT * 1.18;

            row.find('.montantht').val(montantHT.toFixed(2));
            if (!isTPS) {
                row.find('.montantttc').val(montantTTC.toFixed(2));
            }
          }

          function calculateTotals() {
            let totalHT = 0;
            let totalTTC = 0;

            $('#ligneProduits tr').each(function() {
                const ht = parseFloat($(this).find('.montantht').val()) || 0;
                const ttc = parseFloat($(this).find('.montantttc').val()) || 0;
                totalHT += ht;
                totalTTC += ttc;
            });

            $('#totalHT').val(totalHT.toFixed(2));
            if (!isTPS) {
                $('#totalTTC').val(totalTTC.toFixed(2));
            }
          }

          // Form validation
          $('form').submit(function(e) {
              let isValid = true;
              const errors = [];

              // Validate required fields
              if (!$('input[name="dateOperation"]').val()) {
                  errors.push('La date de vente est obligatoire');
                  isValid = false;
              }

              // Validate at least one product line
              let hasValidLine = false;
              $('#ligneProduits tr').each(function() {
                  const productId = $(this).find('.form-select').val();
                  const qte = $(this).find('.qte').val();
                  
                  if (productId && qte && qte > 0) {
                      hasValidLine = true;
                  }
              });

              if (!hasValidLine) {
                  errors.push('Veuillez ajouter au moins une ligne de vente valide');
                  isValid = false;
              }

              if (!isValid) {
                  e.preventDefault();
                  alert(errors.join('\\n'));
              }
          });

  </script>

@endsection
