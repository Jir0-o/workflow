<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all();
        return view('role.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'roleName' => 'required|string|max:255',
            'permissions' => 'array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);
    
        $role = Role::create(['name' => $request->roleName,
    'guard_name' => 'web']);
        $role->permissions()->sync($request->permissions);
    
        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $permissions = Permission::all();
        $role = Role::find($id);
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('role.edit', compact('role','permissions','rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'roleName' => 'required|string|max:255',
            'permissions' => 'array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);
    
        $role = Role::findOrFail($id);
        $role->name = $request->input('roleName');
        $role->save();
    
        $role->permissions()->sync($request->permissions);
    
    
        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
