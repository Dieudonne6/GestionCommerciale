<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TableauController extends Controller
{
    public function tableaudebord(){

    // dd([
    //     'user' => auth()->user(),
    //     'role_relation' => auth()->user()->role ?? null,
    //     'role_libelle' => auth()->user()->role->libelle ?? null,
    // ]);

        return view('pages.tableaudebord');
    }
}