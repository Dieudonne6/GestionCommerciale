<div class="startbar d-print-none">
    <!-- LOGO -->
    <div class="brand">
        <a href="{{ url('/tableaudebord') }}" class="logo">
            <span>
                <img id="logo-image" src="assets/logoo.jpg" alt="Logo">
            </span>
        </a>
    </div>

    <!-- MENU PRINCIPAL -->
    <div class="startbar-menu">
        <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
            <div class="d-flex align-items-start flex-column w-100">
                <ul class="navbar-nav mb-auto w-100">
                    <!-- Tableau de Bord -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/tableaudebord') }}">
                            <i class="iconoir-home-simple menu-icon"></i>
                            <span>Tableau de Bord</span>
                        </a>
                    </li>

                    <!-- Fournisseurs & Achats -->
                    <li class="nav-item">
                        <a class="nav-link" href="#menuFournisseurs" data-bs-toggle="collapse">
                            <i class="iconoir-truck menu-icon"></i>
                            <span>Fournisseurs & Achats</span>
                        </a>
                        <div class="collapse" id="menuFournisseurs">
                            <ul class="nav flex-column">
                                <li class="nav-item"><a class="nav-link"
                                        href="{{ url('categoriesFournisseur') }}">Catégories de fournisseurs</a></li>
                                <li class="nav-item"><a class="nav-link"
                                        href="{{ url('fournisseur') }}">Fournisseurs</a></li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#submenuAchats" data-bs-toggle="collapse">Commandes
                                        d'Achat</a>
                                    <div class="collapse" id="submenuAchats">
                                        <ul class="nav flex-column ms-3">
                                            <li class="nav-item"><a class="nav-link"
                                                    href="{{ url('commandeAchat') }}">Nouvelle Commande</a></li>
                                            <li class="nav-item"><a class="nav-link"
                                                    href="{{ url('receptions') }}">Réception des Commandes Achats</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- GESTION COMMERCIALE -->
                    <li class="nav-item">
                        <a class="nav-link" href="#menuProduits" data-bs-toggle="collapse">
                            <i class="iconoir-box menu-icon"></i>
                            <span>Produits & Stocks</span>
                        </a>
                        <div class="collapse" id="menuProduits">
                            <ul class="nav flex-column">
                                <li class="nav-item"><a class="nav-link" href="{{ url('familleProduit') }}">Familles de
                                        produits</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ url('categorieProduit') }}">Catégories
                                        de produits</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ url('Produits') }}">Produits</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('stocks.consulter') }}">Consulter les
                                        stocks</a></li>
                                {{-- <li class="nav-item"><a class="nav-link"
                                        href="{{ route('stocks.ajuster') }}">Ajustement des stocks</a></li> --}}
                                {{-- <li class="nav-item"><a class="nav-link"
                                        href="{{ url('stocks/ajustement') }}">Ajustement des stocks</a></li> --}}
                                <li class="nav-item"><a class="nav-link"
                                        href="{{ url('inventaires') }}">Inventaire</a>
                                    </li>
                                    <li class="nav-item">
                                      <a class="nav-link" href="{{ route('transferts.index') }}">Transferts entre magasins</a></li>
                                

                            </ul>
                        </div>
                    </li>

                    <!-- GESTION CLIENT -->
                    <li class="nav-item">
                        <a class="nav-link" href="#menuClients" data-bs-toggle="collapse">
                            <i class="iconoir-box menu-icon"></i>
                            <span>Gestion des Clients</span>
                        </a>
                        <div class="collapse" id="menuClients">
                            <ul class="nav flex-column">
                                <li class="nav-item"><a class="nav-link" href="{{ url('/categorieclient') }}">Catégories
                                        de clients</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ url('/clients') }}">Clients</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- GESTION DES TRANSACTIONS -->
                    <li class="nav-item">
                        <a class="nav-link" href="#menuVentes" data-bs-toggle="collapse">
                            <i class="iconoir-wallet menu-icon"></i>
                            <span>Ventes & Factures</span>
                        </a>
                        <div class="collapse" id="menuVentes">
                            <ul class="nav flex-column">
                                <li class="nav-item"><a class="nav-link" href="{{ url('facturation') }}">Liste des Factures</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ url('ventes') }}">Ventes</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- PARAMÈTRES & UTILISATEURS -->
                    <li class="nav-item">
                        <a class="nav-link" href="#menuUtilisateurs" data-bs-toggle="collapse">
                            <i class="iconoir-user-settings menu-icon"></i>
                            <span>Paramètrage</span>
                        </a>
                        <div class="collapse" id="menuUtilisateurs">
                            <ul class="nav flex-column">
                                <li class="nav-item"><a class="nav-link"
                                        href="{{ url('/entreprise') }}">Entreprises</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ url('exercice') }}">Exercice</a>
                                </li>
                                <li class="nav-item"><a class="nav-link"
                                        href="{{ url('/utilisateurs') }}">Utilisateurs</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ url('magasins') }}">Magasins</a>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="{{ url('/modepaiement')}}">Modes de Paiement</a>
                                </li>
                                <li class="nav-item">
                                    <form action="{{ route('fermeture.journee') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="nav-link btn btn-link text-start w-100">
                                            Fermeture de la journée
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
