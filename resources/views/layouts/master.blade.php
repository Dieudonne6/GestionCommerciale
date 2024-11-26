<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">

<head>
  <meta charset="utf-8" />
  <title>@yield('title')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
  <meta content="" name="author" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
  <link href="{{ asset('assets/libs/simple-datatables/style.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
  
</head>
<style>

.modal-header {
    background-color: #fff !important;
  }
  .modal-title {
    color: #000 !important;
  }
</style>
<body>
  @include('layouts.sidebar')
  @include('layouts.navbar')
  <div class="page-wrapper">
    <div class="page-content">
      @yield('content')
      @include('layouts.footer')
    </div>
  </div>
  
  
  <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
  
  <script src="{{ asset('assets/libs/simple-datatables/umd/simple-datatables.js') }}"></script>
  <script src="{{ asset('assets/js/pages/datatable.init.js') }}"></script>
  
  <script src="{{ asset('assets/js/app.js') }}"></script>
</body>

</html>
<style>
  .taille {
    width: 200px !important;
    height: 150px !important;
  }
</style>

<script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoImage = document.getElementById('logo-image');
            // Assurez-vous que l'attribut 'data-sidebar-size' est initialisé à 'default' au chargement
            if (!document.body.hasAttribute('data-sidebar-size') || document.body.getAttribute('data-sidebar-size') !== 'default') {
            document.body.setAttribute('data-sidebar-size', 'default');
            logoImage.src = 'logo.png';
            logoImage.classList.add('taille');
            }

        });

</script>


<script>
  document.addEventListener('DOMContentLoaded', function() {
    const logoImage = document.getElementById('logo-image');
    const menuToggleButton = document.querySelector('.mobile-menu-btn'); // Assurez-vous que c'est le bon sélecteur
    const startbar = document.querySelector('.startbar'); // Sélecteur pour l'élément avec la classe startbar
    
    // Gestionnaire pour le bouton du menu
    menuToggleButton.addEventListener('click', function() {
      if (document.body.getAttribute('data-sidebar-size') === 'default') {
        logoImage.src = 'logo.png';
        logoImage.classList.add('taille');
      } else {
        logoImage.src = 'assets/logoo.jpg';
        logoImage.classList.remove('taille');
      }
    });
    
    // Gestionnaire pour l'effet hover sur la startbar
    startbar.addEventListener('mouseover', function() {
      logoImage.src = 'logo.png'; // Image pour l'effet hover
      logoImage.classList.add('taille'); // Classe pour un style spécifique
      
    });
    
    startbar.addEventListener('mouseout', function() {
      if (document.body.getAttribute('data-sidebar-size') === 'default') {
        logoImage.src = 'logo.png';
        logoImage.classList.add('taille');
      } 
      else {
        logoImage.src = 'assets/logoo.jpg';
        logoImage.classList.remove('taille');
      }
      
    });
  });

</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
      var alerts = document.querySelectorAll('.alert-dismissible');
      alerts.forEach(function(alert) {
        setTimeout(function() {
          alert.style.display = 'none';
        }, 6000);
      });
    });
</script>


