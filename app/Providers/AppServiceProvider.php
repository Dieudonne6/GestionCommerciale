<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Exercice;
use Illuminate\Support\Facades\View;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Partager la variable dans toutes les vues
        $exerciceAct = Exercice::where('statut', 1)->first();
        $exerciceActif = $exerciceAct ? $exerciceAct->annee : null;

        View::share('exerciceActif', $exerciceActif);
    }


}
