
<div class="startbar d-print-none">
    <!--start brand-->
    <div class="brand">
      <a href="index.html" class="logo">
        <span>
          <img id="logo-image" src="assets/logoo.jpg">
        </span>
      </a>
    </div>
    @php
    $routesFacture = ['fournisseur', 'clients'];
    @endphp
    <!--end brand-->
    <!--start startbar-menu-->
    <div class="startbar-menu">
      <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
        <div class="d-flex align-items-start flex-column w-100">
          <!-- Navigation -->
          <ul class="navbar-nav mb-auto w-100">
            <li class="nav-item">
              <a class="nav-link" href="index.html">
                <i class="iconoir-home-simple menu-icon"></i>
                <span>Tableaux de bord</span>
              </a>
            </li><!--end nav-item-->
            <li class="nav-item">
              <a class="nav-link" href="#sidebarElements" data-bs-toggle="collapse" role="button" aria-expanded="false"
                aria-controls="sidebarElements">
                <i class="iconoir-agile menu-icon"></i>
                <span>Approvisionnement</span>
              </a>
              <div class="collapse " id="sidebarElements">
                <ul class="nav flex-column">
                  <li class="nav-item">
                    <a class="nav-link" href="{{url('commandeAchat')}}">Commande d'achats</a>
                  </li><!--end nav-item-->
                  <li class="nav-item">
                    <a class="nav-link" href="{{ url('reception') }}">Réception</a>
                  </li><!--end nav-item-->
              </div><!--end startbarElements-->
            </li><!--end nav-item-->
            <li class="nav-item">
              <a class="nav-link" href="#sidebarAdvancedUI" data-bs-toggle="collapse" role="button"
                aria-expanded="false" aria-controls="sidebarAdvancedUI">
                <i class="iconoir-archive menu-icon"></i>
                <span>Facturation</span>
              </a>
              <div class="collapse " id="sidebarAdvancedUI">
                <ul class="nav flex-column">
                  <li class="nav-item">
                    <a class="nav-link" href="advanced-animation.html">Ventes</a>
                  </li><!--end nav-item-->
                  <li class="nav-item">
                    <a class="nav-link" href="advanced-clipboard.html">Livraisons</a>
                  </li><!--end nav-item-->
                  <li class="nav-item">
                    <a class="nav-link" href="advanced-dragula.html">Rapport de ventes</a>
                </ul><!--end nav-->
              </div><!--end startbarAdvancedUI-->
            </li><!--end nav-item-->
            <li class="nav-item">
              <a class="nav-link" href="#sidebarForms" data-bs-toggle="collapse" role="button" aria-expanded="false"
                aria-controls="sidebarForms">
                <i class="iconoir-journal-page menu-icon"></i>
                <span>Définitions</span>
              </a>
              <div class="collapse" id="sidebarForms">
                <ul class="nav flex-column">
                  <li class="nav-item">
                    <a class="nav-link" href="{{ url('client') }}">Clients</a>
                  </li><!--end nav-item-->
                  <li class="nav-item">
                    <a class="nav-link" href="{{ url('fournisseur') }}">Fournisseurs</a>
                  </li><!--end nav-item-->
                  <li class="nav-item">
                    <a class="nav-link" href="{{ url('categories') }}">Catégories</a>
                  </li><!--end nav-item-->
                  <li class="nav-item">
                    <a class="nav-link" href="{{ url('produits') }}">Produits</a>
                  </li><!--end nav-item-->
                  <li class="nav-item">
                    <a class="nav-link" href="{{ url('/caisses') }}">Caisses</a>
                  </li><!--end nav-item-->
                </ul><!--end nav-->
              </div><!--end startbarForms-->
            </li><!--end nav-item-->
            <li class="nav-item">
              <a class="nav-link" href="#sidebarCharts" data-bs-toggle="collapse" role="button" aria-expanded="false"
                aria-controls="sidebarCharts">
                <i class="iconoir-settings menu-icon"></i>
                <span>Paramètrages</span>
              </a>
              <div class="collapse " id="sidebarCharts">
                <ul class="nav flex-column">
                  <li class="nav-item">
                    <a class="nav-link" href="{{ url('/parametres/utilisateurs') }}">Utilisateurs</a>
                  </li><!--end nav-item-->
                  <li class="nav-item">
                    <a class="nav-link" href="{{ url('/parametres/entreprise') }}">Entreprises</a>
                  </li><!--end nav-item-->
                  <li class="nav-item">
                    <a class="nav-link" href="{{ url('/parametres/roles') }}">Rôles</a>
                  </li><!--end nav-item-->
                  <li class="nav-item">
                    <a class="nav-link" href="{{ url('/parametre/exercice') }}">Exercice</a>
                  </li><!--end nav-item-->
                </ul><!--end nav-->
              </div><!--end startbarCharts-->
            </li><!--end nav-item-->
            <li class="nav-item">
              <a class="nav-link" href="#sidebarTables" data-bs-toggle="collapse" role="button" aria-expanded="false"
                aria-controls="sidebarTables">
                <i class="iconoir-table-rows menu-icon"></i>
                <span>Stock</span>
              </a>
              <div class="collapse " id="sidebarTables">
                <ul class="nav flex-column">
                  <li class="nav-item">
                    <a class="nav-link" href="tables-basic.html">Etat de stock</a>
                  </li><!--end nav-item-->
                  <li class="nav-item">
                    <a class="nav-link" href="tables-datatable.html">Mouvements produits</a>
                  </li><!--end nav-item-->
                </ul><!--end nav-->
              </div><!--end startbarTables-->
            </li><!--end nav-item-->
            <li class="nav-item">
              <a class="nav-link" href="index.html">
                <i class="iconoir-home-simple menu-icon"></i>
                <span>Import/Exports</span>
              </a>
            </li>
        </div>
      </div><!--end startbar-collapse-->
    </div><!--end startbar-menu-->
  </div><!--end startbar-->
  <div class="startbar-overlay d-print-none"></div>