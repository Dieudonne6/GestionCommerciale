<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FournisseurController extends Controller
{
    public function fournisseur(){
        return view('pages.definition.fournisseur');
    }

    public function client(){
        return view('pages.definition.client');
    }
    public function  ajouterfournisseurs(){
        return view('pages.definition.fournisseur');
    }
}
