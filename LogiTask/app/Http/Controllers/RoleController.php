<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json(['roles' => $roles], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'role_name'=>'required|max:255'
            ]);
        } catch (ValidationException $e) {
            return response()->json(["success" => false, 'error'=>$e->getMessage()], 400);
        }

        $role = Role::create([
            'role_name' => $request->role_name,
        ]);
        return response()->json(["success" => true, 'role'=>$role], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $request->validate([
                'id'=>'required|exits:roles,id'
            ]);
        } catch (ValidationException $e) {
            return response()->json(["success" => false, 'error'=>$e->getMessage()], 400);
        }

        $role = Role::find($request->id);
        $role->delete();
        return response()->json(["success" => true, 'message'=>'Role deleted'], 200);
    }
}
