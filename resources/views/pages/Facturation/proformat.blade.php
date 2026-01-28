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
                            <h4 class="card-title">Liste des factures Proformat</h4><br>
                        </div>
                        @if (Session::has('status'))
                            <br>
                            <div class="alert alert-success alert-dismissible">
                            {{Session::get('status')}}
                            </div>
                            @endif

                            @if (Session::has('erreur'))
                            <br>
                            <div class="alert alert-danger alert-dismissible">
                            {{Session::get('erreur')}}
                            </div>
                        @endif
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
                                    @foreach ($allProforma as $proforma)
                                        <tr>
                                            <td>{{ $proforma->reference }}</td>
                                            <td>{{ $proforma->nomClient ?: ($proforma->client ? $proforma->client->identiteCl : 'Non défini') }}</td>
                                            <td>{{ $proforma->dateOperation }}</td>
                                            <td>{{ number_format($proforma->montantTotal  , 0, ',', '.') }}</td>
                                            <td>
                                                {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#ModifyBoardModal{{ $vente->idV }}"> Modifier</button> --}}
                                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteBoardModal{{ $proforma->idProforma }}">
                                                    Supprimer</button>

                                                    <a href=" {{ route('duplicataproforma', $proforma->idProforma) }} " class="btn btn-secondary" >Imprimer</a>
                                                {{-- <button type="button" class="btn btn-secondary" data-bs-toggle="modal"
                                                    data-bs-target="#deleteBoardModal{{ $proforma->idProforma }}">
                                                    Imprimer</button> --}}
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="deleteBoardModal{{ $proforma->idProforma }}" tabindex="-1"
                                            aria-labelledby="deleteBoardModal{{ $proforma->idProforma }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation de
                                                            suppression</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Êtes-vous sûr de vouloir supprimer cet Proforma? <br><br>

                                                            <form method="POST"
                                                            action="{{ route('deleteProforma', $proforma->idProforma) }}">
                                                            @csrf
                                                            {{-- @method('DELETE') --}}                                                  
                                                        </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Annuler</button>

                                                            <button type="submit" class="btn btn-danger">For sure</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
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
                    <h5 class="modal-title">Créer une facture Proforma</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

            <form method="POST" action="{{ route('storeProforma') }}">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="numfacture">Numéro Facture</label>
                            <input type="text" id="numfacture" name="numFacture" class="form-control" value="{{ $numProforma }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="dateOperation">Date Operation</label>
                            <input type="datetime-local" id="dateOperation" name="dateOperation" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="nomclient">Nom Client</label>
                            <input type="text" id="nomclient" name="nomClient" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="telclient">Contact Client</label>
                            <input type="text" id="telclient" name="telClient" class="form-control">
                        </div>                       
                    </div>
                    <div class="d-flex justify-content-between align-items-end mb-2">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="ajouterVente()">
                            + Ajouter une ligne
                        </button>
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

                                    <!-- champ hidden pour envoyer le libelle -->
                                    <input type="hidden" name="lignes[0][libelle]" class="libelle">
                                    <input type="hidden" name="lignes[0][taxe]" class="form-control taxe">
                                    <input type="hidden" name="lignes[0][stock]" class="form-control stock-disponible">
                                </td>
                                <td>
                                    <input type="number" name="lignes[0][qte]" class="form-control qte">
                                </td>
                                <td>
                                    <input type="number" name="lignes[0][prixU]" class="form-control prixU" readonly>
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
                                    <button type="button" class="btn btn-danger" onclick="supprimerLigne(this)">X</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label">Récapitulatif</label>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Total HT:</label>
                                    <input type="text" id="totalHT" name="totalHT" class="form-control" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Total TTC:</label>
                                    <input type="text" id="totalTTC" name="totalTTC" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- <script>
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
</script> --}}

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
            // initializeSelect2();
            
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
            if (isTPS) {
                        const ligne = `<tr>
                          <td>
                            <select id="productSelect" name="lignes[${ligneIndex}][idP]" class="form-select product-select2">
                              <option value="">Sélectionner un produit</option>
                              @foreach ($allproduits as $produit)
                                  <option value="{{ $produit->idPro }}">{{ $produit->libelle }}</option>
                              @endforeach
                            </select>
                              <input type="hidden" name="lignes[${ligneIndex}][libelle]" class="libelle">
                              <input type="hidden" name="lignes[${ligneIndex}][taxe]" class="taxe">
                              <input type="hidden" name="lignes[${ligneIndex}][stock]" class="stock-disponible"
                            </td>
                          <td>
                            <input type="number" name="lignes[${ligneIndex}][qte]"
                            class="form-control qte">
                          </td>
                          <td>
                            <input type="number" name="lignes[${ligneIndex}][prixU]"
                            class="form-control prixU" readonly>
                          </td>
                          <td>
                            <input type="number" name="lignes[${ligneIndex}][montantht]"
                            class="form-control montantht" readonly>
                            <input type="hidden" name="lignes[${ligneIndex}][montantttc]"
                            class="form-control montantttc">
                          </td>
                          <td>
                            <button type="button" class="btn btn-danger"
                            onclick="supprimerLigne(this)">X</button>
                          </td>
                        </tr>`;

                            document.getElementById('ligneProduits').insertAdjacentHTML('beforeend', ligne);
                            ligneIndex++;
                            attachEventListeners();
                    } else {
                        const ligne = `<tr>
                          <td>
                            <select id="productSelect" name="lignes[${ligneIndex}][idP]" class="form-select product-select2">
                              <option value="">Sélectionner un produit</option>
                              @foreach ($allproduits as $produit)
                                  <option value="{{ $produit->idPro }}">{{ $produit->libelle }}</option>
                              @endforeach
                            </select>
                              <input type="hidden" name="lignes[${ligneIndex}][libelle]" class="libelle">
                              <input type="hidden" name="lignes[${ligneIndex}][taxe]" class="taxe">
                              <input type="hidden" name="lignes[${ligneIndex}][stock]" class="stock-disponible"
                            </td>
                          <td>
                            <input type="number" name="lignes[${ligneIndex}][qte]"
                            class="form-control qte">
                          </td>
                          <td>
                            <input type="number" name="lignes[${ligneIndex}][prixU]"
                            class="form-control prixU" readonly>
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
                            onclick="supprimerLigne(this)">X</button>
                          </td>
                        </tr>`;

                            document.getElementById('ligneProduits').insertAdjacentHTML('beforeend', ligne);
                            ligneIndex++;
                            attachEventListeners();
                    }

            // initializeSelect2();
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
                        console.log('produit info', data);
                        // Le prix reste tel quel (TTC dans la base)
                        row.find('.prixU').val(data.prix);
                        row.find('.libelle').val(data.libelle);
                        row.find('.taxe').val(data.taxe);
                        row.find('.stock-disponible').val(data.stock);
                        calculateRowTotal(row);
                        calculateTotals();
                    });
                }
            });

            // Quand la quantité change
            // $(document).on('input', '.qte', function () {
            //     const row = $(this).closest('tr');
            //     const qteSaisie = parseFloat($(this).val()) || 0;
            //     const stock = parseFloat(row.find('.stock-disponible').val()) || 0;

            //     if (qteSaisie > stock) {
            //         alert(`Stock insuffisant ! Disponible : ${stock}`);
            //         $(this).val(stock);
            //     }

            //     calculateRowTotal(row);
            //     calculateTotals();
            // });
          }

        //   function initializeSelect2() {
        //     $('.product-select2').select2({
        //         placeholder: "Sélectionner un produit",
        //         allowClear: false,
        //         width: '100%'
        //     });
            
        //     // Re-initialize Select2 when modal is shown
        //     $('.modal').on('shown.bs.modal', function() {
        //         $(this).find('.product-select2').select2({
        //             placeholder: "Sélectionner un produit",
        //             allowClear: false,
        //             width: '100%',
        //             dropdownParent: $(this)
        //         });
        //     });
        //   }

            function calculateRowTotal(row) {
            const qte = parseFloat(row.find('.qte').val()) || 0;
            const prixU = parseFloat(row.find('.prixU').val()) || 0;
            
            // Le prixU est déjà TTC dans la base
            // const montantHT = qte * prixU;
            // const montantTTC = isTPS ? montantHT : montantHT * 1.18; // Convertir en HT seulement si TVA

            // const montantTTC = qte * prixU ;
            // const montantHT = montantTTC / 1.18 ;
            
            // Le prixU est déjà TTC dans la base
            const montantTTC = qte * prixU;
            const montantHT = isTPS ? montantTTC : montantTTC / 1.18; // Convertir en HT seulement si TVA

            row.find('.montantht').val(montantHT.toFixed(2));
            row.find('.montantttc').val(montantTTC.toFixed(2));
            // if (!isTPS) {
            //     row.find('.montantttc').val(montantTTC.toFixed(2));
            // }
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
            $('#totalTTC').val(totalTTC.toFixed(2));
            // if (!isTPS) {
            //     $('#totalTTC').val(totalTTC.toFixed(2));
            // }
          }

          // Form validation
          $('#addBoaModal').submit(function(e) {
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
