<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Resources\RoleResource;
use App\Http\Resources\RoleCollection;
use App\Models\Module;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::withCount('users')->get();

        // return RoleCollection::collection($roles);

        return response()->json($roles);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        try {
            $role = Role::create(['name' => $request->input('name')]);
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }

        return message('Role created successfully', 200, $role);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $role->load('permissions');

        return RoleResource::make($role);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        if ($role->name == 'Admin')
            return message("You can't delete Admin role", 400);

        if ($role->delete())
            return message('Role archived successfully');

        return message('Something went wrong', 400);
    }


    public function getPermission()
    {
        $modules = Module::with('permissions')->get();

        return response()->json($modules);
    }


    public function updatePermission(Request $request, Role $role)
    {
        if ($request->attach) {
            $role->givePermissionTo($request->permission_id);
        } else {
            $role->revokePermissionTo($request->permission_id);
        }

        return message('Permission updated Successfully');

        // switch ($request->assign_multiple) {
        //     case true:
        //         if($request->assign_permission){

        //             $permissions = DB::table('permissions')->pluck('id');
        //             $role->syncPermissions($permissions);
        //         }
        //         else{
        //             $role->syncPermissions(([]));
        //         }

        //         break;
        //     case false:
        //         if ($request->assign_permission) {
        //             $role->givePermissionTo($request->permission_id);
        //             return response()->json(['message'=>'Permission updated Successfully']);
        //         }
        //         else {

        //             $role->revokePermissionTo($request->permission_id);
        //         }

        //     default:
        //         break;
        // }


    }
}
