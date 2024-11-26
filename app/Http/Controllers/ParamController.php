<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ParamController extends Controller
{
    public function utilisateurs(){
        return view('pages.parametres.utilisateurs');
    }
}