<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EnsureDatabaseConnection
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && session()->has('selected_database')) {
            $selectedDatabase = session('selected_database');

            // Mettre à jour la configuration de la base de données
            config(['database.connections.mysql.database' => $selectedDatabase]);
            DB::purge('mysql');
            DB::reconnect('mysql');
        }

        return $next($request);
    }
}