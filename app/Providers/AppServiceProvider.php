<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
// use App\Models\Exercice;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Blade;



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
    // public function boot(): void
    // {
    //     // Partager la variable dans toutes les vues
    //     // $exerciceAct = Exercice::where('statut', 1)->first();
    //     // $exerciceActif = $exerciceAct ? $exerciceAct->annee : null;

    //     // View::share('exerciceActif', $exerciceActif);

    //     View::composer('*', function ($view) {

    //     $notifications = DB::table('stockes')
    //         ->join('produits', 'produits.idPro', '=', 'stockes.idPro')
    //         ->select(
    //             'produits.libelle',
    //             'produits.stockMinimum',
    //             'stockes.qteStocke'
    //         )
    //         ->get()
    //         ->filter(function ($item) {
    //             return $item->qteStocke <= $item->stockMinimum;
    //         })
    //       ->map(function ($item) {

    //             if ($item->qteStocke == 0) {
    //                 $item->type = 'rupture';
    //                 $item->texte = "est en rupture de stock";
    //             } else {
    //                 $item->type = 'risque';
    //                 $item->texte = "est en risque de rupture de stock";
    //             }

    //             return $item;
    //         });

    //     $view->with('stockNotifications', $notifications);
    // }); 
    // }


    public function boot(): void
    {
        /**
         * 1️⃣ Définition des permissions menus
         */
        Blade::if('canMenu', function ($menuCode, $action = 'view') {
            $user = auth()->user();
            if (!$user || !$user->role) return false;

            return \App\Models\Menu::where('code', $menuCode)
                ->whereHas('roles', function ($q) use ($user, $action) {
                    $q->where('roles.idRole', $user->idRole)
                    ->where("can_$action", 1);
                })
                ->exists();
        });


        Blade::if('canAnyMenu', function (array $menus, $action = 'view') {
            $user = auth()->user();
            if (!$user || !$user->role) return false;

            return \App\Models\Menu::whereIn('code', $menus)
                ->whereHas('roles', function ($q) use ($user, $action) {
                    $q->where('roles.idRole', $user->idRole)
                    ->where("can_$action", 1);
                })
                ->exists();
        });
        /**
         * 2️⃣ Notifications stock (TON CODE ACTUEL)
         */
        View::composer('*', function ($view) {

            $notifications = DB::table('stockes')
                ->join('produits', 'produits.idPro', '=', 'stockes.idPro')
                ->select(
                    'produits.libelle',
                    'produits.stockMinimum',
                    'stockes.qteStocke'
                )
                ->get()
                ->filter(function ($item) {
                    return $item->qteStocke <= $item->stockMinimum;
                })
                ->map(function ($item) {
                    if ($item->qteStocke == 0) {
                        $item->type = 'rupture';
                        $item->texte = "est en rupture de stock";
                    } else {
                        $item->type = 'risque';
                        $item->texte = "est en risque de rupture de stock";
                    }
                    return $item;
                });

            $view->with('stockNotifications', $notifications);
        });
    }

}
