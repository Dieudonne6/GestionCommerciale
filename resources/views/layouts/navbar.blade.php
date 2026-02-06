<div class="startbar d-print-none">
    <!-- LOGO -->
    <div class="brand">
        <a href="{{ url('/tableaudebord') }}" class="logo">
            <span>
                <img id="logo-image" src="assets/logoo.jpg" alt="Logo">
            </span>
        </a>
    </div>

    @php
        $role = auth()->user()->role->libelle;
    @endphp

    <!-- MENU PRINCIPAL -->
    <div class="startbar-menu">
        <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
            <div class="d-flex align-items-start flex-column w-100">
                <ul class="navbar-nav mb-auto w-100">
                    <!-- Tableau de Bord -->
                    @canMenu('tableaudebord','view')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/tableaudebord') }}">
                                <i class="iconoir-home-simple menu-icon"></i>
                                <span>Tableau de Bord</span>
                            </a>

                            {{-- @canMenu('ventes','view')
                                <a href="{{ url('ventes') }}">Ventes</a>
                            @endcanMenu --}}
                        </li>
                    @endcanMenu

                    @canAnyMenu(['categoriesF','fournisseur','commandeAchat', 'receptions'],'view')
                        <!-- Fournisseurs & Achats -->
                        <li class="nav-item">
                            <a class="nav-link" href="#menuFournisseurs" data-bs-toggle="collapse">
                                <i class="iconoir-truck menu-icon"></i>
                                <span>Fournisseurs & Achats</span>
                            </a>
                            <div class="collapse" id="menuFournisseurs">
                                <ul class="nav flex-column">
                                    @canMenu('categoriesF','view')
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('categoriesFournisseur') }}">Catégories de fournisseurs</a>
                                        </li>
                                    @endcanMenu

                                    @canMenu('fournisseur','view')
                                        <li class="nav-item">
                                            <a class="nav-link"
                                                href="{{ url('fournisseur') }}">Fournisseurs
                                            </a>
                                        </li>
                                    @endcanMenu

                                    @canAnyMenu(['commandeAchat','receptions'],'view')
                                        <li class="nav-item">
                                            <a class="nav-link" href="#submenuAchats" data-bs-toggle="collapse">Commandes
                                                d'Achat</a>
                                            <div class="collapse" id="submenuAchats">
                                                <ul class="nav flex-column ms-3">
                                                    @canMenu('commandeAchat','view')
                                                        <li class="nav-item">
                                                            <a class="nav-link" href="{{ url('commandeAchat') }}">Nouvelle Commande</a>
                                                        </li>
                                                    @endcanMenu

                                                    @canMenu('receptions','view')
                                                        <li class="nav-item">
                                                            <a class="nav-link" href="{{ url('receptions') }}">Réception des Commandes Achats</a>
                                                        </li>
                                                    @endcanMenu
                                                </ul>
                                            </div>
                                        </li>
                                    @endcanAnyMenu
                                </ul>
                            </div>
                        </li>
                    @endcanAnyMenu



                    @canAnyMenu(['familleProduit','categorieProduit','Produits', 'consulterStocks', 'inventaires','transferts' ],'view')
                        <!-- GESTION COMMERCIALE -->
                        <li class="nav-item">
                            <a class="nav-link" href="#menuProduits" data-bs-toggle="collapse">
                                <i class="iconoir-box menu-icon"></i>
                                <span>Produits & Stocks</span>
                            </a>
                            <div class="collapse" id="menuProduits">
                                <ul class="nav flex-column">
                                    @canMenu('familleProduit','view')
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('familleProduit') }}">Familles de produits</a>
                                        </li>
                                    @endcanMenu

                                    @canMenu('categorieProduit','view')
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('categorieProduit') }}">Catégories de produits</a>
                                        </li>
                                    @endcanMenu

                                    @canMenu('Produits','view')
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('Produits') }}">Produits</a>
                                        </li>
                                    @endcanMenu

                                    @canMenu('consulterStocks','view')
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('consulterStocks') }}">Consulter les stocks</a>
                                        </li>
                                    @endcanMenu

                                        {{-- <li class="nav-item"><a class="nav-link"
                                                href="{{ route('stocks.ajuster') }}">Ajustement des stocks</a></li> --}}
                                        {{-- <li class="nav-item"><a class="nav-link"
                                                href="{{ url('stocks/ajustement') }}">Ajustement des stocks</a></li> --}}

                                    @canMenu('inventaires','view')
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('inventaires') }}">Inventaire</a>
                                        </li>
                                    @endcanMenu

                                    @canMenu('transferts','view')
                                        <li class="nav-item"> 
                                            <a class="nav-link" href="{{ route('transferts') }}">Transferts entre magasins</a>
                                        </li>
                                    @endcanMenu
                                </ul>
                            </div>
                        </li>
                    @endcanAnyMenu

                    @canAnyMenu(['categorieclient','clients'],'view')
                        <!-- GESTION CLIENT -->
                        {{-- <li class="nav-item">
                            <a class="nav-link" href="#menuClients" data-bs-toggle="collapse">
                                <i class="iconoir-box menu-icon"></i>
                                <span>Gestion des Clients</span>
                            </a>
                            <div class="collapse" id="menuClients">
                                <ul class="nav flex-column">
                                    @canMenu('categorieclient','view')
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('/categorieclient') }}">Catégories de clients</a>
                                        </li>
                                    @endcanMenu

                                    @canMenu('clients','view')
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('/clients') }}">Clients</a>
                                        </li>
                                    @endcanMenu
                                </ul>
                            </div>
                        </li> --}}
                    @endcanAnyMenu
                    

                    @canAnyMenu(['ventes','facturation','proformat'],'view')
                        <!-- GESTION DES TRANSACTIONS -->
                        <li class="nav-item">
                            <a class="nav-link" href="#menuVentes" data-bs-toggle="collapse">
                                <i class="iconoir-wallet menu-icon"></i>
                                <span>Ventes & Factures</span>
                            </a>

                                <div class="collapse {{ Route::is('ventes', 'ajouterVente.store', 'facturation', 'duplicatafacture', 'storeProforma', 'proformat', 'duplicataproforma') ? 'show' : '' }}"
                                    id="menuVentes">
                                <ul class="nav flex-column">

                                    @canMenu('ventes','view')
                                        <li class="nav-item">
                                            <a class="nav-link {{ Route::is('ventes', 'ajouterVente.store') ? 'active' : '' }}" href="{{ url('ventes') }}">
                                                Ventes
                                            </a>
                                        </li>
                                    @endcanMenu

                                    @canMenu('facturation','view')
                                        <li class="nav-item">
                                            <a class="nav-link {{ Route::is('facturation', 'duplicatafacture') ? 'active' : '' }}" href="{{ url('facturation') }}">
                                                Liste des Factures
                                            </a>
                                        </li>
                                    @endcanMenu

                                    @canMenu('proformat','view')
                                        <li class="nav-item">
                                            <a class="nav-link {{ Route::is('storeProforma', 'proformat', 'duplicataproforma') ? 'active' : '' }}" href="{{ url('proforma') }}">
                                                Proforma
                                            </a>
                                        </li>
                                    @endcanMenu

                                </ul>

                            </div>
                        </li>
                    @endcanAnyMenu

                    @php
                        $isAdmin = auth()->user()->role->libelle === 'Administrateur';
                    @endphp


                    
                    @if(
                        $isAdmin
                        || auth()->user()->canAnyMenu(['magasins','fermetures'], 'view')
                    )
                        <!-- PARAMÈTRES & UTILISATEURS -->
                        <li class="nav-item">
                            <a class="nav-link" href="#menuUtilisateurs" data-bs-toggle="collapse">
                                <i class="iconoir-user-settings menu-icon"></i>
                                <span>Paramètrage</span>
                            </a>
                            <div class="collapse" id="menuUtilisateurs">
                                <ul class="nav flex-column">

                                    @if($isAdmin)
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('/entreprise') }}">Entreprises</a>
                                        </li>
                                    @endif

                                    @if($isAdmin)
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('exercice') }}">Exercice</a>
                                        </li>
                                    @endif

                                    
                                    @if($isAdmin)
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('/categorie_tarifaire') }}">Categorie Tarifaires</a>
                                        </li>
                                    @endif
                                    

                                    @if($isAdmin)
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('/utilisateurs') }}">Utilisateurs</a>
                                        </li>
                                    @endif

                                    @canMenu('magasins','view')
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('magasins') }}">Magasins</a>
                                        </li>
                                    @endcanMenu

                                    @if($isAdmin)
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('/modepaiement')}}">Modes de Paiement</a>
                                        </li>
                                    @endif

                                    @if($isAdmin)
                                        <li class="nav-item"><a class="nav-link" href="{{ url('/delaiAlert')}}">Paramétrages</a>
                                        </li>
                                    @endif
                                    
                                    @canMenu('fermetures','view')
                                        <li class="nav-item">
                                            <form action="{{ route('fermeture.journee') }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="nav-link btn btn-link text-start w-100">
                                                    Fermeture de la journée
                                                </button>
                                            </form>
                                        </li>
                                    @endcanMenu

                                    @if($isAdmin)
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('/menu-permissions')}}">Gestion des permission</a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </li>
                    @endif

                </ul>
            </div>
        </div>
    </div>
</div>