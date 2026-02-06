<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
// use App\Models\Exercice;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Blade;

use Carbon\Carbon;
use App\Models\DetailReceptionCmdAchat;
use App\Models\DetailVente;


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

            /* ============================
            1️ ALERTES STOCK MINIMUM
            ============================ */
            $stockMinNotifications = DB::table('stockes')
                ->join('produits', 'produits.idPro', '=', 'stockes.idPro')
                ->select(
                    'produits.libelle',
                    'produits.stockMinimum',
                    'stockes.qteStocke'
                )
                ->get()
                ->filter(fn ($item) => $item->qteStocke <= $item->stockMinimum)
                ->map(function ($item) {
                    $item->type = ($item->qteStocke == 0) ? 'rupture' : 'risque';
                    $item->texte = ($item->qteStocke == 0)
                        ? "est en rupture de stock"
                        : "est en risque de rupture de stock";
                    return $item;
                });

            /* ============================
            2️ ALERTES PRODUITS PÉRISSABLES
            ============================ */
            $today = Carbon::today();
            $peremptionNotifications = collect();

            $lots = DetailReceptionCmdAchat::with('detailCommandeAchat.produit')
                ->whereDate('alert', '<=', $today)
                ->whereDate('expiration', '>=', $today)
                ->get();
            
            Carbon::setLocale('fr');

            foreach ($lots as $lot) {

                $detailCom = $lot->detailCommandeAchat;
                if (!$detailCom) continue;

                $idPro     = $detailCom->idPro;
                $libelle   = $detailCom->produit->libelle;
                $qteCible  = $lot->qteReceptionne;
                $dateLot   = $lot->created_at;
                $expiration= $lot->expiration;

                // Nombre de produits réceptionnés
                $npr = DetailReceptionCmdAchat::whereIn('idDetailCom', function ($q) use ($idPro, $dateLot) {
                        $q->select('idDetailCom')
                        ->from('detail_commande_achats')
                        ->where('idPro', $idPro)
                        ->whereBetween('created_at', [$dateLot, now()]);
                    })
                    ->sum('qteReceptionne');

                // Nombre de produits vendus
                $npv = DetailVente::where('idPro', $idPro)
                    ->whereBetween('created_at', [$dateLot, now()])
                    ->sum('qte');

                $recepVendu = $npr - $npv;
                $nprApres   = $npr - $qteCible;

                if ($recepVendu > $nprApres) {

                    $reste = $recepVendu - $nprApres;

                   $reste = $recepVendu - $nprApres;

                    if ($reste > 0) {
                        $peremptionNotifications->push((object)[
                            'libelle'        => $libelle,
                            'type'           => 'peremption',
                            'qte'            => $qteCible,
                            'date_reception' => $dateLot->translatedFormat('d F Y'),
                            'date_expiration'=> Carbon::parse($expiration)->translatedFormat('d F Y'),
                            'reste'          => $reste,
                        ]);
                    }


                }
            }

            /* ============================
            3️ FUSION DES NOTIFICATIONS
            ============================ */
            $allNotifications = $stockMinNotifications
                ->merge($peremptionNotifications);

            $view->with('stockNotifications', $allNotifications);
        
        });
    }

}
