<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        return Role::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomRole' => 'required|string|max:255',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create($validated);

        return response()->json($role, 201);
    }

    public function show(string $id)
    {
        return Role::findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'nomRole' => 'sometimes|required|string|max:255',
            'permissions' => 'nullable|array',
        ]);

        $role->update($validated);

        return response()->json($role);
    }

    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json(null, 204);
    }
}
