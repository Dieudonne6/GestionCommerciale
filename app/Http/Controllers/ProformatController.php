<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProformatController extends Controller
{
    public function index(){
        return view ('pages.Facturation.proformat');
    }
}
