@extends('layouts.master')
@section('content')
<div class="row justify-content-center">
  <div class="col-md-12 col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col">                      
            <h4 class="card-title">Ajouter une commande</h4>                      
          </div><!--end col-->
        </div>  <!--end row-->                                  
      </div><!--end card-header-->
      <div class="card-body pt-0">
        <form>
          <div class="row">
            <div class="col-md-4">
              <div class="mb-3">
                <label for="username" class="form-label">Date de commande</label>
                <input class="form-control mb-3" type="datetime-local" name="dateOperation">
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label" for="useremail">Délais de livraison</label>
                <input type="text" class="form-control" id="useremail" name="delai" required="">
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Date de remise</label>
                <input type="datetime-local" name="dateRemise" class="form-control" id="exampleInputPassword1" placeholder="">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Fournisseur</label>
                <select id="" name="idF" class="form-select">
                  
                  @foreach ($allfournisseurs as $allfournisseur)
                  <option value="{{ $allfournisseur->identiteF }}">{{ $allfournisseur->identiteF }}</option>
                  @endforeach
                </select> 
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Commande Achat</label>
                <input type="text" class="form-control" name="descCmd" id="exampleInputPassword1" placeholder="">
              </div>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table mb-0 checkbox-all" id="datatable_1">
              <thead class="table-light">
                <tr>
                  <th>N</th>
                  <th >Article</th>
                  <th >Quantité</th>
                  <th>Montant HT</th>
                  <th>TVA</th>
                  <th>Montant TTC</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    u
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBoaModal"><i class="fa-solid fa-plus me-1"></i> Ajouter une ligne</button>
          
          {{-- <button type="submit" class="btn btn-primary">Ajouter une ligne</button> --}}
        </form>                
      </div><!--end card-body--> 
    </div><!--end card--> 
    <div class="modal fade" id="addBoaModal" tabindex="-1" aria-labelledby="addBoaModal" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Ajouter une ligne de commande</h1>
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
          <form id="addLineForm" method="POST">
            @csrf
            <div class="modal-body">
              <div class="mb-2">
                  <select id="productSelect" name="idP" class="form-select">
                      <option value="">Sélectionner un produit</option>
                      @foreach ($allproduits as $allproduit)
                          <option value="{{ $allproduit->idP }}">{{ $allproduit->NomP }}</option>
                      @endforeach
                  </select>
              </div>
              <div class="mb-2">
                  <input type="number" class="form-control" placeholder="Quantité" name="quantity" id="quantity">
              </div>
              <div class="mb-2">
                  <input type="number" class="form-control" placeholder="Prix" name="price" id="price">
              </div>
          </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
              <button type="submit" class="btn btn-primary">Ajouter</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div> 
  @endsection
  <script>
    document.getElementById('addLineForm').addEventListener('submit', function (e) {
      e.preventDefault(); // Empêche le rechargement de la page
      
      // Récupération des données du formulaire
      const idP = document.getElementById('productSelect').value;
      const quantity = document.getElementById('quantity').value;
      const price = document.getElementById('price').value;
      const token = document.querySelector('input[name="_token"]').value;
      
      // Vérification des champs requis
      if (!idP || !quantity || !price) {
        alert('Veuillez remplir tous les champs');
        return;
      }
      
      // Requête AJAX
      fetch('/ajouterlignCmd', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({
          idP: idP,
          quantity: quantity,
          price: price
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Ajout de la ligne au tableau
          const table = document.querySelector('#datatable_1 tbody');
          const newRow = `
                <tr>
                    <td>${data.line.id}</td>
                    <td>${data.line.product}</td>
                    <td>${data.line.quantity}</td>
                    <td>${data.line.price}</td>
                    <td>${data.line.tva}</td>
                    <td>${data.line.ttc}</td>
                </tr>
            `;
          table.insertAdjacentHTML('beforeend', newRow);
          
          // Réinitialisation du formulaire
          document.getElementById('addLineForm').reset();
          
          // Fermeture du modal
          const modal = bootstrap.Modal.getInstance(document.getElementById('addBoaModal'));
          modal.hide();
        } else {
          alert('Erreur lors de l\'ajout de la ligne.');
        }
      })
      .catch(error => console.error('Erreur:', error));
    });
    
  </script>