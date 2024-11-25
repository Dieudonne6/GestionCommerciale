<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FournisseurController extends Controller
{
    public function fournisseur(){
        return view('pages.discipline.fournisseur');
    }
}
