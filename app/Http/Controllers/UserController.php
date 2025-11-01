<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        return User::with('role')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,idRole',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Coordonnées non valides.'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function show(string $id)
    {
        return User::with('role')->findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // Check if user is updating their own profile
        $isOwnProfile = Auth::check() && Auth::id() == $id;

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'current_password' => 'sometimes|string',
            'password' => 'sometimes|string|min:8|confirmed',
            'role_id' => 'sometimes|exists:roles,idRole',
        ]);

        // If password is being changed, verify current password
        if (isset($validated['password'])) {
            if ($isOwnProfile) {
                // User must provide current password when updating their own profile
                if (!isset($validated['current_password'])) {
                    return response()->json([
                        'message' => 'Le mot de passe actuel est requis pour changer le mot de passe.'
                    ], 422);
                }

                // Verify current password
                if (!Hash::check($validated['current_password'], $user->password)) {
                    return response()->json([
                        'message' => 'Le mot de passe actuel est incorrect.'
                    ], 422);
                }
            }

            $validated['password'] = Hash::make($validated['password']);
        }

        // Remove current_password from validated data before update
        unset($validated['current_password']);
        unset($validated['password_confirmation']);

        $user->update($validated);

        // Return user with role relationship
        return response()->json($user->load('role'));
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(null, 204);
    }

    public function me()
    {
        $user = Auth::user(); // récupère l'utilisateur connecté

        return response()->json([
            'nom'   => $user->nom,
            'prenom'=> $user->prenom,
            'role'  => $user->role->nomRole, // relation User -> Role
        ]);
    }

    public function countByRole()
    {
        $stats = User::join('roles', 'users.role_id', '=', 'roles.idRole')
            ->select('roles.nomRole as role', DB::raw('count(users.id) as total'))
            ->groupBy('roles.nomRole')
            ->get();

        return response()->json($stats);
    }

    public function uploadAvatar(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // Verify the authenticated user is updating their own avatar or is an admin
        $isOwnProfile = Auth::id() == $id;
        $isAdmin = Auth::user()->role && Auth::user()->role->nomRole === 'admin';
        
        if (!$isOwnProfile && !$isAdmin) {
            return response()->json([
                'message' => 'Non autorisé à modifier cet avatar.'
            ], 403);
        }

        $validated = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        // Delete old avatar if exists
        if ($user->avatar && file_exists(public_path($user->avatar))) {
            unlink(public_path($user->avatar));
        }

        // Store new avatar
        $file = $request->file('avatar');
        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('images/avatars'), $filename);

        // Update user avatar path
        $avatarPath = '/images/avatars/' . $filename;
        $user->update(['avatar' => $avatarPath]);

        return response()->json([
            'message' => 'Avatar mis à jour avec succès',
            'avatar' => $avatarPath,
            'user' => $user->load('role')
        ]);
    }

}
