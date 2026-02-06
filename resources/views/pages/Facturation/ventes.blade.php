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
                                        <th>Codemecef Fac</th>
                                        <th>Client</th>
                                        <th data-type="date" data-format="YYYY/DD/MM">Date Operation</th>
                                        <th>Montant Total</th>
                                        <th>Montant AIB</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($allVente as $vente)
                                        <tr>
                                            <td>{{ $vente->reference }}</td>
                                            <td>{{ $vente->factureNormalise?->CODEMECEF ?? '-' }}</td>
                                            <td>{{ $vente->nomClient ?: ($vente->client ? $vente->client->identiteCl : 'Non défini') }}</td>
                                            <td>{{ $vente->dateOperation }}</td>
                                            <td>{{ $vente->montantTotal }}</td>
                                            <td>{{ $vente->montant_aib }}</td>
                                            <td>
                                                {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#ModifyBoardModal{{ $vente->idV }}"> Modifier</button> --}}
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
                                                        Êtes-vous sûr de vouloir supprimer cette vente? <br><br>
                                                        <strong>Si oui , Veuillez entrer le CODEMECEF  de la facture de cette vente.</strong><br><br>

                                                            <form method="POST"
                                                            action="{{ route('deletevente', $vente->factureNormalise->idFacture) }}">
                                                            @csrf
                                                            {{-- @method('DELETE') --}}
                                                            <div class="col-md-12 mb-2">
                                                                <label for="codemecef" style="font-weight: bold !important; color: #382f2f">CODEMECEF</label>
                                                                <input class="form-control mb-3" type="text" name="codemecef" required>
                                                            </div>                                                    
                                                        </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Annuler</button>

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

        {{-- lollllll --}}
        
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
                                    <input class="form-control mb-3" type="text" name="nomClient" value="" required>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="telclient" class="form-label">Contact Client</label>
                                    <input class="form-control mb-3" type="text" name="telClient" value="">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="numvente" class="form-label">Numero vente</label>
                                    <input class="form-control mb-3" type="text" name="reference" id="numvente" value="{{ $numVente }}" readonly>
                                </div>
                                <div class="mb-2 col-md-4">
                                    <label for="modepaiement" class="form-label">Mode paiement</label>
                                    <select name="idModPaie" class="form-control" id="modepaiement" required>
                                        <option value="">Sélectionner un mode</option>
                                        @foreach ($modes as $mode)
                                            <option value="{{ $mode->idModPaie }}" {{ strtolower($mode->libelle) == 'ESPECES' ? 'selected' : '' }}>
                                                {{ $mode->libelle }}
                                            </option>
                                            @endforeach
                                        </select>
                                        {{-- <input type="hidden" name="libellModepaie" value="{{ $mode->libelle }}"> --}}
                                        <input type="hidden" name="libelleModePaie" id="libelleModePaie">

                                </div>

                                {{-- <div class="mb-2 col-md-4">
                                    <label for="categorie_tarifaire" class="form-label">Catégorie tarifaire</label>
                                    <select name="categorie_tarifaire_id" id="categorie_tarifaire" class="form-control">
                                        <option value="">Aucune</option>
                                        @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('categorie_tarifaire_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->libelle }} ({{ $cat->type_reduction }} - {{ $cat->valeur_reduction }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div> --}}

                                @php
                                    // s'assurer que $categories est une collection
                                    // récupère l'id par défaut : old() si présent sinon la catégorie code 'STD' si elle existe
                                    $defaultCatId = old('categorie_tarifaire_id') ?? ($categories->firstWhere('code', 'STD')->id ?? null);
                                @endphp

                                <div class="mb-2 col-md-4">
                                    <label for="categorie_tarifaire" class="form-label">Catégorie tarifaire</label>
                                    <select name="categorie_tarifaire_id" id="categorie_tarifaire" class="form-control" required>
                                        <option value="">Aucune</option>
                                        @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}"
                                                data-type="{{ $cat->type_reduction }}"
                                                data-value="{{ $cat->valeur_reduction }}"
                                                {{ $defaultCatId == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->libelle }} ({{ $cat->type_reduction }} - {{ $cat->valeur_reduction }})
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
                                            <input type="text" id="totalHT" name="totalHT" class="form-control" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Total TTC:</label>
                                            <input type="text" id="totalTTC" name="totalTTC" class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-2">
                            <div class="col-md-12">
                                <div id="discountPreview" class="small text-muted"></div>
                                <div id="discountError" class="text-danger small mt-1" style="display:none;"></div>
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
    // exposer les catégories tarifaires au JS
    const CATEGORIES = @json($categoriesJS);

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
        // Vérifier le régime de l'entreprise (à adapter selon votre logique)
        checkRegimeTPS();

        // Si une catégorie est déjà sélectionnée, appliquer tout de suite
        if ($('#categorie_tarifaire').length && $('#categorie_tarifaire').val()) {
            applyCategoryDiscount();
        }

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
                  <input type="hidden" name="lignes[${ligneIndex}][stock]" class="stock-disponible">
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
                onclick="supprimerLigne(this)">Supprimer</button>
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
                  <input type="hidden" name="lignes[${ligneIndex}][stock]" class="stock-disponible">
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
                onclick="supprimerLigne(this)">Supprimer</button>
              </td>
            </tr>`;

            document.getElementById('ligneProduits').insertAdjacentHTML('beforeend', ligne);
            ligneIndex++;
            attachEventListeners();
        }
    }

    function supprimerLigne(button) {
        button.closest('tr').remove();
        calculateTotals();
    }

    /**
     * applyCategoryDiscount
     * - lit les lignes (prix d'origine stocké en data('origUnit'))
     * - calcule discountTotal (entier)
     * - répartit proportionnellement (floor) et corrige le reste sur la 1ère ligne non nulle
     * - met à jour prixU, montantttc et montantht affichés
     */
    function applyCategoryDiscount() {
        const catId = $('#categorie_tarifaire').val();
        // construire tableau de lignes
        const rows = [];
        $('#ligneProduits tr').each(function() {
            const $row = $(this);
            const qty = parseInt($row.find('.qte').val()) || 0;
            const origUnit = Number($row.data('origUnit')) || Number($row.find('.prixU').val()) || 0;
            const lineTotal = Math.round(origUnit * qty); // entier
            rows.push({ $row, qty, origUnit, lineTotal });
        });

        const totalTTC = rows.reduce((s, r) => s + r.lineTotal, 0);

        // si pas de catégorie ou total zero -> remettre valeurs d'origine
        if (!catId || totalTTC === 0) {
            rows.forEach(r => {
                r.$row.find('.prixU').val(Number(r.origUnit).toFixed(2));
                r.$row.find('.montantttc').val(Number(r.lineTotal).toFixed(2));
                const montHT = isTPS ? r.lineTotal : (r.lineTotal / 1.18);
                r.$row.find('.montantht').val(montHT.toFixed(2));
            });
            calculateTotals();
            return;
        }

        const cat = CATEGORIES.find(c => String(c.id) === String(catId));
        if (!cat) { calculateTotals(); return; }

        // calcul discount total (entier)
        let discountTotal = 0;
        if (cat.type === 'pourcentage') {
            discountTotal = Math.round((totalTTC * Number(cat.value)) / 100);
        } else {
            discountTotal = Math.round(Number(cat.value));
            if (discountTotal > totalTTC) discountTotal = totalTTC;
        }

        // repartition proportionnelle (floor) + correction du reste
        const distributed = [];
        let sumShares = 0;
        rows.forEach(r => {
            let share = 0;
            if (totalTTC > 0) {
                share = Math.floor((r.lineTotal * discountTotal) / totalTTC);
            }
            distributed.push(share);
            sumShares += share;
        });

        let remaining = discountTotal - sumShares;
        if (remaining > 0) {
            for (let i = 0; i < distributed.length; i++) {
                if (rows[i].lineTotal > 0) {
                    distributed[i] += remaining;
                    remaining = 0;
                    break;
                }
            }
        }

        // appliquer répartition et mettre à jour DOM
        rows.forEach((r, i) => {
            const share = distributed[i] || 0;
            const newLineTotal = Math.max(0, r.lineTotal - share); // entier
            const newUnitPrice = r.qty > 0 ? (newLineTotal / r.qty) : 0;

            // mettre à jour affichage (prix affiché en 2 décimales, montants affichés arrondis)
            r.$row.find('.prixU').val(newUnitPrice.toFixed(2));
            r.$row.find('.montantttc').val(newLineTotal.toFixed(2));
            const montHT = isTPS ? newLineTotal : (newLineTotal / 1.18);
            r.$row.find('.montantht').val(montHT.toFixed(2));

            // stocker pour soumission si nécessaire
            r.$row.data('newLineTotal', newLineTotal);
            r.$row.data('newUnitPrice', newUnitPrice);
        });

        calculateTotals();
    }

    function attachEventListeners() {
        // override existing handlers safely
        $('.qte, .prixU, .montantttc').off('input').on('input', function() {
            const row = $(this).closest('tr');
            calculateRowTotal(row);
            // si catégorie sélectionnée -> appliquer remise globale
            if ($('#categorie_tarifaire').length && $('#categorie_tarifaire').val()) {
                applyCategoryDiscount();
            } else {
                calculateTotals();
            }
        });

        // product-select handler : store orig price in data and update fields
        $('.product-select2').off('change').on('change', function() {
            const productId = $(this).val();
            const row = $(this).closest('tr');
            
            if (productId) {
                $.get(`/get-produit-info/${productId}`, function(data) {
                    console.log('produit info', data);
                    // Stocke le prix d'origine (TTC) sur la ligne pour pouvoir recalculer si catégorie choisie
                    row.data('origUnit', Number(data.prix) || 0);
                    row.find('.prixU').val(Number(data.prix).toFixed(2));
                    row.find('.libelle').val(data.libelle);
                    row.find('.taxe').val(data.taxe);
                    row.find('.stock-disponible').val(data.stock);
                    calculateRowTotal(row);
                    // si catégorie sélectionnée -> appliquer remise globale
                    if ($('#categorie_tarifaire').length && $('#categorie_tarifaire').val()) {
                        applyCategoryDiscount();
                    } else {
                        calculateTotals();
                    }
                });
            } else {
                // reset
                row.data('origUnit', 0);
                row.find('.prixU').val('');
                row.find('.libelle').val('');
                row.find('.taxe').val('');
                row.find('.stock-disponible').val('');
                calculateRowTotal(row);
                calculateTotals();
            }
        });

        // Quand la quantité change (déjà couvert, mais on ajoute l'appel catégorie)
        $(document).off('input', '.qte').on('input', '.qte', function () {
            const row = $(this).closest('tr');
            const qteSaisie = parseFloat($(this).val()) || 0;
            const stock = parseFloat(row.find('.stock-disponible').val()) || 0;

            if (qteSaisie > stock) {
                alert(`Stock insuffisant ! Disponible : ${stock}`);
                $(this).val(stock);
            }

            calculateRowTotal(row);
            if ($('#categorie_tarifaire').length && $('#categorie_tarifaire').val()) {
                applyCategoryDiscount();
            } else {
                calculateTotals();
            }
        });

        // lorsque la catégorie change -> appliquer immédiatement
        $('#categorie_tarifaire').off('change').on('change', function() {
            applyCategoryDiscount();
        });
    }

    function calculateRowTotal(row) {
        const qte = parseFloat(row.find('.qte').val()) || 0;
        // prixU affiché (peut être le prix d'origine ou déjà réduit)
        const prixU = parseFloat(row.find('.prixU').val()) || 0;
        
        // montantTTC = qte * prixU
        const montantTTC = qte * prixU;
        const montantHT = isTPS ? montantTTC : (montantTTC / 1.18);

        row.find('.montantht').val(montantHT.toFixed(2));
        row.find('.montantttc').val(montantTTC.toFixed(2));
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
    }


    function getTotalTTCBrut() {
        let total = 0;

        $('#ligneProduits tr').each(function () {
            const qty = parseInt($(this).find('.qte').val()) || 0;
            const origUnit = Number($(this).data('origUnit')) || 0;
            total += Math.round(origUnit * qty);
        });

        return total;
    }

    // ---------- VALIDATION / PREVIEW CATEGORIE TARIFAIRE ----------
    (function(){
    const categorieSelect = document.getElementById('categorie_tarifaire');
    const discountPreviewEl = document.getElementById('discountPreview');
    const discountErrorEl = document.getElementById('discountError');
    const submitBtn = document.querySelector('#addBoaModal button[type="submit"]');

    function parseNumber(n) {
        const v = parseFloat(n);
        return Number.isFinite(v) ? v : 0;
    }
    
    function getCurrentTotalTTC() {
        return getTotalTTCBrut();
    }

    // Récupère catégorie sélectionnée (type et valeur)
    function getSelectedCategorie() {
        if (!categorieSelect) return null;
        const opt = categorieSelect.options[categorieSelect.selectedIndex];
        if (!opt || !opt.value) return null;
        return {
        id: opt.value,
        type: opt.dataset.type || null,            // "pourcentage" ou "fixe"
        value: parseFloat(opt.dataset.value) || 0  // valeur numérique
        };
    }

    // Calcule réduction en montant absolu (entier)
    function computeDiscountAmount(totalTTC, categorie) {
        if (!categorie) return 0;
        if (categorie.type === 'pourcentage') {
        return Math.round((totalTTC * categorie.value) / 100);
        } else { // 'fixe'
        return Math.round(categorie.value);
        }
    }

    // Met à jour l'aperçu et valide
    function updateDiscountPreviewAndValidate() {
        const total = getTotalTTCBrut();
        const cat = getSelectedCategorie();
        discountErrorEl.style.display = 'none';
        discountErrorEl.textContent = '';
        if (!cat) {
        discountPreviewEl.innerHTML = '';
        if (submitBtn) submitBtn.disabled = false;
        return;
        }

        const discount = computeDiscountAmount(total, cat);
        const totalAfter = Math.max(0, total - discount);

        // Affichage lisible (avec arrondi entier)
        discountPreviewEl.innerHTML = `Réduction : <strong>${discount.toLocaleString()}</strong> — Total après réduction : <strong>${totalAfter.toLocaleString()}</strong>`;

        // Contrôle : si type fixe et réduction >= total => bloquer avec message clair
        if (cat.type === 'fixe' && discount >= total) {
        discountErrorEl.style.display = 'block';
        discountErrorEl.textContent = "Réduction fixe trop élevée — la réduction ne peut pas être supérieure ou égale au total. Choisissez une autre catégorie ou ajustez les lignes.";
        if (submitBtn) submitBtn.disabled = true;
        return;
        }

        // Autre règle : si pourcentage donne totalAfter <= 0 (rare) -> bloquer aussi
        if (cat.type === 'pourcentage' && totalAfter <= 0) {
        discountErrorEl.style.display = 'block';
        discountErrorEl.textContent = "Réduction trop importante — le total après réduction est nul ou négatif.";
        if (submitBtn) submitBtn.disabled = true;
        return;
        }

        // tout va bien
        if (submitBtn) submitBtn.disabled = false;
    }

    // When category changes, recalc
    if (categorieSelect) {
        categorieSelect.addEventListener('change', function() {
        updateDiscountPreviewAndValidate();
        });
    }

    // Hook dans calculateTotals : on appelle validate après chaque recalcul
    // Si ta fonction calculateTotals est une function globale, on peut la "wrap"
    if (typeof calculateTotals === 'function') {
        const originalCalculateTotals = calculateTotals;
        window.calculateTotals = function() {
        originalCalculateTotals();
        updateDiscountPreviewAndValidate();
        };
        // appel initial
        updateDiscountPreviewAndValidate();
    } else {
        // fallback : on vérifie toutes les 300ms (uniquement si calculateTotals non dispo)
        setInterval(updateDiscountPreviewAndValidate, 300);
    }

    })();




    // Form validation (inchangé)
    $('#addBoaModal').submit(function(e) {
        let isValid = true;
        const errors = [];

        if (!$('input[name="dateOperation"]').val()) {
            errors.push('La date de vente est obligatoire');
            isValid = false;
        }

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
        } else {
            // Optionnel : avant le submit, forcer l'arrondi entier pour l'API si besoin
            // parcourir lignes et remplacer montantttc par int(arrondi)
            $('#ligneProduits tr').each(function() {
                const $row = $(this);
                const qty = parseInt($row.find('.qte').val()) || 0;
                const newUnit = Number($row.data('newUnitPrice')) || Number($row.find('.prixU').val()) || 0;
                const newLineTotal = Math.round(newUnit * qty);
                // si tu veux envoyer les valeurs calculées côté client, décommente les inputs cachés suivants :
                // ensure hidden inputs exist
                if ($row.find('input[name$="[prixU_apres]"]').length === 0) {
                    const idx = $row.index();
                    $row.append(`<input type="hidden" name="lignes[${idx}][prixU_apres]" value="${Math.round(newUnit)}">`);
                    $row.append(`<input type="hidden" name="lignes[${idx}][lineTotal_apres]" value="${newLineTotal}">`);
                } else {
                    $row.find('input[name$="[prixU_apres]"]').val(Math.round(newUnit));
                    $row.find('input[name$="[lineTotal_apres]"]').val(newLineTotal);
                }
            });
        }
    });
</script>


<script>
    document.getElementById('modepaiement').addEventListener('change', function () {
        let selectedOption = this.options[this.selectedIndex];
        document.getElementById('libelleModePaie').value = selectedOption.text;
    });
</script>


@endsection




        {{-- @foreach ($allVente as $vente) --}}
            {{-- Modal de modification --}}
            {{-- <div class="modal fade" id="ModifyBoardModal{{ $vente->idV }}" tabindex="-1"
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
            </div> --}}
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
        {{-- @endforeach --}}


        
        