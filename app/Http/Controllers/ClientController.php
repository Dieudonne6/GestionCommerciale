<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Http\Requests\ClientRequest;


class ClientController extends Controller
{

    public function client(){

        $allclients = Client::get();
        return view('pages.definition.client', compact('allclients'));
    }
        // creation client

        public function ajouterClient( ClientRequest $request ) {


            // Vérifier si le client existe déjà
            $clientExiste = Client::where('NomCl', $request->input('NomCl'))
            ->where('PrenomCl', $request->input('PrenomCl'))
            ->exists();
    
            if ($clientExiste) {
                // Retourner une erreur si le client existe déjà
                return back()->with(['errors' => 'Ce client existe déjà.']);
            }
    
            // creer un nouveau client dans le cas echeant
            $Client = new Client();
            $Client->NomCl = $request->input('NomCl');
            $Client->PrenomCl = $request->input('PrenomCl');
            $Client->AdresseCl = $request->input('AdresseCl');
            $Client->ContactCl = $request->input('ContactCl');
            $Client->save();
    
            return back()->with("status", "Le client a ete creer avec succes");
        }
    
    
        // suppression client
    
        public function deleteClient ($id) {
            $client = Client::where('idCl', $id)->first();
            $client->delete();
            return back()->with("status", "Le client a ete supprimer avec succes");
        }
    
    
        // modification client
    
        public function updateClient ( ClientRequest $request, $id ) {
            $modifClient = Client::where('idCl', $id)->first();
            $modifClient->NomCl = $request->input('NomCl');
            $modifClient->PrenomCl = $request->input('PrenomCl');
            $modifClient->AdresseCl = $request->input('AdresseCl');
            $modifClient->ContactCl = $request->input('ContactCl');
            $modifClient->update();  
            return back()->with("status", "Le client a ete modifier avec succes");
  
        }
}
