<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TableauController extends Controller
{
    public function tableaudebord(){
        return view('pages.tableaudebord');
    }
}