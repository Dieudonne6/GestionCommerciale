<div class="topbar d-print-none">
    <div class="container-xxl">
        <nav class="topbar-custom d-flex justify-content-between" id="topbar-custom">


            <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">
                <li>
                    <button class="nav-link mobile-menu-btn nav-icon" id="togglemenu">
                        <i class="iconoir-menu-scale"></i>
                    </button>
                </li>
                <li class="mx-3 welcome-text">
                    {{-- <h3 class="mb-0 fw-bold text-truncate">Good Morning, James!</h3> --}}
                    <!-- <h6 class="mb-0 fw-normal text-muted text-truncate fs-14">Here's your overview this week.</h6> -->
                </li>
            </ul>
            <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">
                <li class="hide-phone app-search">
                    <a class="nav-link dropdown-toggle arrow-none nav-icon" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        {{-- {{ $exerciceActif }} --}}
                        {{-- <img src="assets/images/flags/us_flag.jpg" alt="" class="thumb-sm rounded-circle"> --}}
                    </a>
                </li>
                <li class="dropdown">
                    <a class="nav-link dropdown-toggle arrow-none nav-icon" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <img src="{{ asset('assets/images/flags/us_flag.jpg') }}" alt=""
                            class="thumb-sm rounded-circle">
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#"><img
                                src="{{ asset('assets/images/flags/us_flag.jpg') }}" alt="" height="15"
                                class="me-2">English</a>
                        {{-- <a class="dropdown-item" href="#"><img src="assets/images/flags/spain_flag.jpg" alt="" height="15"
                  class="me-2">Spanish</a>
                 <a class="dropdown-item" href="#"><img src="assets/images/flags/germany_flag.jpg" alt="" height="15"
                  class="me-2">German</a> --}}
                        <a class="dropdown-item" href="#"><img
                                src="{{ asset('assets/images/flags/french_flag.jpg') }}" alt="" height="15"
                                class="me-2">French</a>
                    </div>
                </li><!--end topbar-language-->

                <li class="topbar-item">
                    <a class="nav-link nav-icon" href="javascript:void(0);" id="light-dark-mode">
                        <i class="icofont-moon dark-mode"></i>
                        <i class="icofont-sun light-mode"></i>
                    </a>
                </li>

                <li class="dropdown topbar-item">
                    <a class="nav-link dropdown-toggle arrow-none nav-icon" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="icofont-bell-alt"></i>
                        <span class="alert-badge">
                            {{ $stockNotifications->count() }}
                        </span>

                    </a>
                    <div class="dropdown-menu stop dropdown-menu-end dropdown-lg py-0">

                        <h5 class="dropdown-item-text m-0 py-3 d-flex justify-content-between align-items-center">
                            Notifications <a href="#" class="badge text-body-tertiary badge-pill">
                                {{-- <i class="iconoir-plus-circle fs-4"></i> --}}
                            </a>
                        </h5>
                        <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified mb-1" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link mx-0 active" data-bs-toggle="tab" href="#All" role="tab"
                                    aria-selected="true">
                                    Etat du stock des produits <span class="badge bg-primary-subtle text-primary badge-pill ms-1"> {{ $stockNotifications->whereIn('type',['rupture','risque'])->count() }} </span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link mx-0" data-bs-toggle="tab" href="#Projects" role="tab"
                                    aria-selected="false" tabindex="-1">
                                   Produits périssables
                                    <span class="badge bg-warning-subtle text-warning badge-pill ms-1">
                                        {{ $stockNotifications->where('type','peremption')->count() }}
                                    </span>

                                </a>
                            </li>
                            
                        </ul>
                        <div class="ms-0" style="max-height:230px;" data-simplebar>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="All" role="tabpanel"
                                    aria-labelledby="all-tab" tabindex="0">
                                    @if($stockNotifications->whereIn('type',['rupture','risque'])->count() > 0)
                                        @foreach($stockNotifications->whereIn('type',['rupture','risque']) as $notif)
                                            <a href="#" class="dropdown-item py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 bg-danger-subtle text-danger thumb-md rounded-circle">
                                                        <i class="icofont-warning fs-4"></i>
                                                    </div>
                                                    <div class="flex-grow-1 ms-2">
                                                        <h6 class="my-0 fw-normal text-dark fs-13">
                                                            Le produit <strong>{{ $notif->libelle }}</strong> {{ $notif->texte }}
                                                        </h6>
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="dropdown-item text-center text-muted py-3">
                                            Aucune alerte de stock
                                        </div>
                                    @endif

                                </div>
                                <div class="tab-pane fade" id="Projects" role="tabpanel"
                                    aria-labelledby="projects-tab" tabindex="0">
                                   
                                    @if($stockNotifications->where('type','peremption')->count() > 0)
                                        @foreach($stockNotifications->where('type','peremption') as $notif)
                                            <a href="#" class="dropdown-item py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 bg-warning-subtle text-warning thumb-md rounded-circle">
                                                        <i class="icofont-warning fs-4"></i>
                                                    </div>
                                                    <div class="flex-grow-1 ms-2">
                                                        <h6 class="my-0 fw-normal text-dark fs-13">
                                                            <strong>{{ $notif->libelle }}</strong> :
                                                            <strong>{{ $notif->qte }}</strong> produits reçus le
                                                            <strong>{{ $notif->date_reception }}</strong>
                                                            restent <strong>{{ $notif->reste }}</strong> en stock.
                                                            <br>
                                                            Date d’expiration :
                                                            <strong>{{ $notif->date_expiration }}</strong>
                                                            Assurez-vous de les libérer avant cette date sous risque de 
                                                        </h6>

                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="dropdown-item text-center text-muted py-3">
                                            Aucun produit périssable en stock
                                        </div>
                                    @endif                          
                                </div>                          
                            </div>
                        </div>                     
                    </div>
                </li>

                <li class="dropdown topbar-item">
                    <a class="nav-link dropdown-toggle arrow-none nav-icon" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <img src="{{ Auth::user()->photo ? 'data:image/jpeg;base64,' . base64_encode(Auth::user()->photo) : asset('assets/images/users/avatar-1.jpg') }}"
                            alt="" class="thumb-lg rounded-circle">
                    </a>
                    <div class="dropdown-menu dropdown-menu-end py-0">
                        <div class="d-flex align-items-center dropdown-item py-2 bg-secondary-subtle">
                            <div class="flex-shrink-0">
                                <img src="{{ Auth::user()->photo ? 'data:image/jpeg;base64,' . base64_encode(Auth::user()->photo) : asset('assets/images/users/avatar-1.jpg') }}"
                                    alt="" class="thumb-md rounded-circle">
                            </div>
                            <div class="flex-grow-1 ms-2 text-truncate align-self-center">
                                <h6 class="my-0 fw-medium text-dark fs-13">{{ Auth::user()->nom }}</h6>
                                {{-- <small class="text-muted mb-0">Front End Developer</small> --}}
                            </div><!--end media-body-->
                        </div>
                        <div class="dropdown-divider mt-0"></div>
                        <small class="text-muted px-2 pb-1 d-block">Compte</small>
                        <a class="dropdown-item" href="{{ url('profile') }}"><i
                                class="las la-user fs-18 me-1 align-text-bottom"></i> Profil</a>
                        <a class="dropdown-item" href="{{ route('password.change') }}"><i
                                class="las la-wallet fs-18 me-1 align-text-bottom"></i>
                            Modifier mot de passe</a>
                        {{-- <small class="text-muted px-2 py-1 d-block">Settings</small>
                    <a class="dropdown-item" href="pages-profile.html"><i
                        class="las la-cog fs-18 me-1 align-text-bottom"></i>Account Settings</a>
                    <a class="dropdown-item" href="pages-profile.html"><i
                        class="las la-lock fs-18 me-1 align-text-bottom"></i> Security</a>
                    <a class="dropdown-item" href="pages-faq.html"><i
                        class="las la-question-circle fs-18 me-1 align-text-bottom"></i> Help Center</a> --}}
                        <div class="dropdown-divider mb-0"></div>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a class="dropdown-item text-danger" href="#"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="las la-power-off fs-18 me-1 align-text-bottom"></i> Déconnexion
                        </a>
                    </div>
                </li>
            </ul><!--end topbar-nav-->
        </nav>
        <!-- end navbar-->
    </div>
</div>
<style>
    .dropdown-item h6 {
        white-space: normal;
        word-break: break-word;
    }
</style>