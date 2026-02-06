<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Role;
use DB;

class MenuPermissionController extends Controller
{
     public function index()
    {
        $menus = Menu::orderBy('id')->get();
        $roles = Role::orderBy('idRole')->get();
        return view('pages.admin.menu_permissions', compact('menus','roles'));
    }

    public function updatePermissions(Request $request)
    {
        $data = $request->input('permissions', []); // format attendu explained below

            DB::transaction(function() use ($data) {
                foreach ($data as $menuId => $rolesPermissions) {
                    foreach ($rolesPermissions as $roleId => $perms) {
                        \DB::table('menu_role')->updateOrInsert(
                            ['menu_id' => $menuId, 'idRole' => $roleId],
                            [
                                'can_view'   => intval($perms['view']   ?? 0),
                                'can_create' => intval($perms['create'] ?? 0),
                                'can_edit'   => intval($perms['edit']   ?? 0),
                                'can_delete' => intval($perms['delete'] ?? 0),
                                'updated_at' => now(),
                                'created_at' => now(),
                            ]
                        );
                    }
                }
            });

        return response()->json(['success'=>true, 'message'=>'Permissions mises à jour']);
    }
}
