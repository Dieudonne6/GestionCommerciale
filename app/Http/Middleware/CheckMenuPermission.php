<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\Auth\Access\AuthorizationException;


class CheckMenuPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
//    public function handle(Request $request, Closure $next, $menuCode, $action = 'view')
//     {
//         $user = $request->user();
//         if (!$user) abort(403);

//         $role = $user->role; // suppose relation Utilisateur->role existe
//         if (!$role) abort(403);

//         $menu = Menu::where('code', $menuCode)->first();
//         if (!$menu) abort(403);

//         // récupérer pivot
//         $pivot = $menu->roles()->where('roles.idRole', $role->idRole)->first();
//         if (!$pivot) abort(403);

//         $flag = match($action) {
//             'view' => $pivot->pivot->can_view,
//             'create' => $pivot->pivot->can_create,
//             'edit' => $pivot->pivot->can_edit,
//             'delete' => $pivot->pivot->can_delete,
//             default => false,
//         };

//         if (!$flag) abort(403, 'Accès non autorisé');

//         return $next($request);
//     }


    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $menuCode, $action = 'view')
    {
        $user = $request->user();
        $role = $user?->role;
        $menu = Menu::where('code', $menuCode)->first();
        $pivot = $menu?->roles()->where('roles.idRole', $role?->idRole)->first();

        // Vérifier la permission
        $hasPermission = match($action) {
            'view' => $pivot?->pivot->can_view ?? false,
            'create' => $pivot?->pivot->can_create ?? false,
            'edit' => $pivot?->pivot->can_edit ?? false,
            'delete' => $pivot?->pivot->can_delete ?? false,
            default => false,
        };

        // ❌ Si pas de permission, lancer AuthorizationException
        if (!$hasPermission) {
            throw new AuthorizationException(
                "Vous n'avez pas les droits nécessaires pour accéder à cette page."
            );
        }

        return $next($request);
    }
}
