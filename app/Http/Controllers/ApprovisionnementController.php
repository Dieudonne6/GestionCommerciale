<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApprovisionnementController extends Controller
{
    //

    public function commandeAchat() {
        return view('pages.Approvisionnement.commandeAchat');
    }
    public function reception() {
        return view('pages.Approvisionnement.reception');
    }
}
