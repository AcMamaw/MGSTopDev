<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    // Display all roles
    public function index()
    {
        $roles = Role::all(); // fetch all roles from DB
        return view('management.role', compact('roles'));
    }

    // Store a new role
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'role_name' => 'required|unique:roles,role_name',
            'description' => 'nullable|string',
        ]);

        // Create new role
        $role = Role::create([
            'role_name' => $request->role_name,
            'description' => $request->description,
        ]);

        // Return JSON for frontend AJAX
        return response()->json([
            'role_id' => $role->role_id,
            'role_name' => $role->role_name,
            'description' => $role->description,
        ]);
    }

    public function fetch()
    {
        $roles = Role::all();
        return response()->json($roles);
    }

}
