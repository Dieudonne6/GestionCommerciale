@extends('layouts.master')
@section('content')
<!-- Page Content-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="container-xxl">
  
  
  <div class="row justify-content-center">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col">
              <h4 class="card-title">Liste des commandes d'achat</h4> </br>
            </div><!--end col-->
            <div class="col-auto">
              <div class="col-auto">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBoaModal"><i class="fa-solid fa-plus me-1"></i> Ajouter une commande</button>
              </div><!--end col-->
            </div><!--end col-->
          </div> <!--end row-->
        </div><!--end card-header-->
        @if(Session::has('status'))
        <div id="statusAlert" class="alert alert-success">
          {{ Session::get('status')}}
        </div>
        @endif
        @if(Session::has('erreur'))
        <div id="statusAlert" class="alert alert-danger">
          {{ Session::get('erreur')}}
        </div>
        @endif
        <div class="card-body pt-0">
          <div class="table-responsive">
            <table class="table datatable" id="datatable_1">
              <thead class="table-light">
                <tr>
                  <th>No Commande</th>
                  <th>Fournisseur</th>
                  <th data-type="date" data-format="YYYY/DD/MM">Date Operation</th>
                  <th data-type="date" data-format="YYYY/DD/MM">Date Remise</th>
                  <th>Montant HT</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($allcommande as $allcmd)
                <tr>
                  <td>{{ $allcmd->numCmd }}</td>
                  <td>{{ $allcmd->fournisseur ? $allcmd->fournisseur->identiteF : 'Non défini' }}</td>
                  
                  <td>{{ $allcmd->dateOperation }}</td>
                  <td>{{ $allcmd->dateRemise }}</td>
                  <td>{{ $allcmd->montantHT }}</td>
                  <td>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModifyBoardModal{{$allcmd->idCmd}}"> Modifier</button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBoardModal{{$allcmd->idCmd}}"> Supprimer</button>
                  </td>
                </tr>
                
                <div class="modal fade" id="deleteBoardModal{{$allcmd->idCmd}}" tabindex="-1" aria-labelledby="deleteBoardModal{{$allcmd->idCmd}}" aria-hidden="true">
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
                </div>
                @endforeach
              </tbody>
            </table>
          </div>
        </div><!--end card-body-->
      </div><!--end card-->
    </div> <!--end col-->
  </div><!--end row-->
  @foreach ($allcommande as $allcmd)
  <div class="modal fade" id="ModifyBoardModal{{$allcmd->idCmd}}" tabindex="-1" aria-labelledby="ModifyBoardModal{{$allcmd->idCmd}}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Modifier une Commande</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{url('modifCmd/'.$allcmd->idCmd)}}" method="POST">
          @csrf
          @method('PUT')
          <div class="modal-body">
            <div class="row mb-3">
              <div class="col-md-4 mb-2">
                <label for="username" class="form-label">Fournisseur</label>
                <select id="" name="identitefr" class="form-select">
                  <option value="">Sélectionner un fournisseur</option>
                  @foreach ($allfournisseurs as $allfournisseur)
                  <option value="{{ $allfournisseur->idF }}" 
                    {{ $allfournisseur->idF == $allcmd->idF ? 'selected' : '' }}>
                    {{ $allfournisseur->identiteF }}
                  </option>
                  @endforeach
                </select> 
              </div>
              <div class="col-md-4 mb-2">
                <label for="username" class="form-label">Numero commande</label>
                <input class="form-control mb-3" type="text" name="numCmd" value="{{$allcmd->numCmd}}">
              </div>
              <div class="col-md-4 mb-2">
                <label for="username" class="form-label">Date de commande</label>
                <input class="form-control mb-3" type="datetime-local" name="dateOperation" value="{{$allcmd->dateOperation}}">
              </div>
            </div>
            <div class="row">
              <div class="mb-2 col-md-4">
                <label class="form-label" for="useremail">Délais de livraison</label>
                <input type="text" class="form-control" id="useremail" name="delai" value="{{$allcmd->delai}}">            
              </div>
              <div class="mb-2 col-md-4">
                <label for="exampleInputPassword1" class="form-label">Date de remise</label>
                <input type="datetime-local" name="dateRemise" class="form-control" id="exampleInputPassword1"  value="{{$allcmd->dateRemise}}">     
              </div>
              <div class="col-md-4">
                <label for="exampleInputPassword1" class="form-label">Description Commande</label>
                <input type="text" class="form-control" name="descCmd" id="exampleInputPassword1" value="{{$allcmd->descCmd}}">
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                  <label class="form-label">Commande</label>
                  <button type="button" class="btn btn-secondary my-2 mx-3" onclick="ajouterCmde($allcmd->idCmd)">Ajouter une commande</button>
                </div>                                    
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Article</th>
                      <th>Quantité</th>
                      <th>Montant HT</th>
                      <th>TVA</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody id="ligneProduis">
                     @foreach ($allcmd->lignesCommande as $index => $ligne)
                     <input type="hidden" name="lignes[{{$index}}][idLCmd]" value="{{ $ligne->idLCmd }}">
                    <tr>
                      <td>
                        <select id="productSelect" name="lignes[{{$index}}][idP]" class="form-select">
                          <option value="">Sélectionner un produit</option>
                          @foreach ($allproduits as $allproduit)
                          <option value="{{ $allproduit->idP }}" {{ $ligne->idP == $allproduit->idP ? 'selected' : '' }}>{{ $allproduit->NomP }}</option>
                          @endforeach
                        </select>
                      </td>
                      <td>
                        <input type="number" name="lignes[{{$index}}][qte]" class="form-control qte" value="{{ $ligne->qteCmd }}">
                      </td>
                      <td>
                        <input type="number" name="lignes[{{$index}}][montantht]" class="form-control montantht" value="{{ $ligne->prix }}">
                      </td>
                      <td>
                        <input type="number" name="lignes[{{$index}}][tva]" class="form-control tva" value="{{ $ligne->TVA }}">
                      </td>
                      <td>
                        <button type="button" class="btn btn-danger delete-ligne" data-id="{{ $ligne->idLCmd }}">Supprimer</button>
                        
                      </td>
                    </tr>
                    
                    @endforeach 
                  </tbody>
                </table> 
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
   <div class="modal fade" id="delteBoardModal{{$allcmd->idCmd}}" tabindex="-1" aria-labelledby="delteBoardModal{{$allcmd->idCmd}}" aria-hidden="true">
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
  </div> 
  @endforeach
  
  <div class="modal fade" id="addBoaModal" tabindex="-1" aria-labelledby="addBoaModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
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
        <form method="POST" action="{{ route('ajouterCmd.store') }}">
          @csrf
          <div class="modal-body">
            <div class="row mb-3">
              <div class="col-md-4 mb-2">
                <label for="username" class="form-label">Fournisseur</label>
                <select id="" name="identitefr" class="form-select">
                  <option value="">Sélectionner un fournisseur</option>
                  @foreach ($allfournisseurs as $allfournisseur)
                  <option value="{{ $allfournisseur->idF }}">{{ $allfournisseur->identiteF }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4 mb-2">
                <label for="username" class="form-label">Numero commande</label>
                <input class="form-control mb-3" type="text" name="numCmd">
              </div>
              <div class="mb-2 col-md-4">
                <label class="form-label" for="useremail">Délais de livraison</label>
                <input type="text" class="form-control" id="useremail" name="delai">            
              </div>
            </div>
            <div class="row">
              <div class="col-md-4 mb-2">
                <label for="username" class="form-label">Date de commande</label>
                <input class="form-control mb-3" type="datetime-local" name="dateOperation">
              </div>
              <div class="mb-2 col-md-4">
                <label for="exampleInputPassword1" class="form-label">Date de remise</label>
                <input type="datetime-local" name="dateRemise" class="form-control" id="exampleInputPassword1" placeholder="">     
              </div>
              <div class="col-md-4">
                <label for="exampleInputPassword1" class="form-label">Description Commande</label>
                <input type="text" class="form-control" name="descCmd" id="exampleInputPassword1" placeholder="">
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                  <label class="form-label">Commande</label>
                  <button type="button" class="btn btn-secondary my-2 mx-3" onclick="ajouterCmd()">Ajouter une commande</button>
                </div>                                    
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Article</th>
                      <th>Quantité</th>
                      <th>Montant HT</th>
                      <th>TVA</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody id="ligneProduits">
                    <tr>
                      <td>
                        <select id="productSelect" name="lignes[0][idP]" class="form-select">
                          <option value="">Sélectionner un produit</option>
                          @foreach ($allproduits as $allproduit)
                          <option value="{{ $allproduit->idP }}">{{ $allproduit->NomP }}</option>
                          @endforeach
                        </select>
                      </td>
                      <td>
                        <input type="number" name="lignes[0][qte]"
                        class="form-control qte">
                      </td>
                      <td>
                        <input type="number" name="lignes[0][montantht]"
                        class="form-control montantht">
                      </td>
                      <td>
                        <input type="number" name="lignes[0][tva]"
                        class="form-control tva">
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
  function ajouterCmd() {
    const ligne = `<tr>
                    <td>
                      <select id="productSelect" name="lignes[${ligneIndex}][idP]" class="form-select">
                        <option value="">Sélectionner un produit</option>
                        @foreach ($allproduits as $allproduit)
                            <option value="{{ $allproduit->idP }}">{{ $allproduit->NomP }}</option>
                        @endforeach
                    </select>
                    </td>
                    <td>
                      <input type="number" name="lignes[${ligneIndex}][qte]"
                      class="form-control qte">
                    </td>
                    <td>
                      <input type="number" name="lignes[${ligneIndex}][montantht]"
                      class="form-control montantht">
                    </td>
                    <td>
                      <input type="number" name="lignes[${ligneIndex}][tva]"
                      class="form-control tva">
                    </td>
                    
                    <td>
                      <button type="button" class="btn btn-danger"
                      onclick="supprimerLigne(this)">Supprimer</button>
                    </td>
                  </tr>`;
      document.getElementById('ligneProduits').insertAdjacentHTML('beforeend', ligne);
      ligneIndex++;
    }
    let ligneIndx = 1;
  function ajouterCmde(idCmd) {
    const lign = `<tr>
                    <td>
                      <select id="productSelect" name="lignes[${ligneIndx}][idP]" class="form-select">
                        <option value="">Sélectionner un produit</option>
                        @foreach ($allproduits as $allproduit)
                            <option value="{{ $allproduit->idP }}">{{ $allproduit->NomP }}</option>
                        @endforeach
                    </select>
                    </td>
                    <td>
                      <input type="number" name="lignes[${ligneIndx}][qte]"
                      class="form-control qte">
                    </td>
                    <td>
                      <input type="number" name="lignes[${ligneIndx}][montantht]"
                      class="form-control montantht">
                    </td>
                    <td>
                      <input type="number" name="lignes[${ligneIndx}][tva]"
                      class="form-control tva">
                    </td>
                    
                    <td>
                      <button type="button" class="btn btn-danger"
                      onclick="supprimerLign(this)">Supprimer</button>
                    </td>
                  </tr>`;
      document.getElementById('ligneProduis').insertAdjacentHTML('beforeend', lign);
      ligneIndx++;
    }
    
    
    function supprimerLigne(button) {
      button.closest('tr').remove();
    }
    function supprimerLign(button) {
      button.closest('tr').remove();
    }
    $(document).ready(function() {
      $(".delete-ligne").click(function() {
        let ligneId = $(this).data("id");
        let row = $(this).closest("tr");
        
        $.ajax({
          url: "{{ url('deleteLigneCommande') }}/" + ligneId, 
          type: "DELETE",
          data: {
            _token: "{{ csrf_token() }}" 
          },
          success: function(response) {
            if (response.success) {
              row.remove(); 
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
  </script>
  
  @endsection
  