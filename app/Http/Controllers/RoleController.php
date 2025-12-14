<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    // Display all roles
    public function index()
    {
        $roles = Role::orderBy('role_id')->get();

        return view('management.role', compact('roles'));
    }

    // Store a new role (AJAX)
    public function store(Request $request)
    {
        $data = $request->validate([
            'role_name'    => 'required|string|max:255|unique:roles,role_name',
            'description'  => 'nullable|string',
        ]);

        $role = Role::create($data);

        return response()->json([
            'role_id'     => $role->role_id,
            'role_name'   => $role->role_name,
            'description' => $role->description,
        ]);
    }

    // Update existing role (AJAX)
    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'role_name'   => 'required|string|max:255|unique:roles,role_name,' . $role->role_id . ',role_id',
            'description' => 'nullable|string',
        ]);

        $role->update($data);

        return response()->json([
            'role_id'     => $role->role_id,
            'role_name'   => $role->role_name,
            'description' => $role->description,
        ]);
    }

    // Optional: fetch all roles as JSON
    public function fetch()
    {
        $roles = Role::orderBy('role_id')->get();

        return response()->json($roles);
    }

        public function archive($id)
    {
        $role = Role::findOrFail($id);
        $role->archive = 'Archived';
        $role->save();

        return response()->json(['status' => 'ok']);
    }

    public function unarchive($id)
    {
        $role = Role::findOrFail($id);
        $role->archive = null;
        $role->save();

        return response()->json(['status' => 'ok']);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete(); // or forceDelete() if using soft deletes
        return response()->json(['status' => 'ok']);
    }
}
