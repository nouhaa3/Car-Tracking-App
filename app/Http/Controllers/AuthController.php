<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\PendingUser;
use App\Models\Notification;
use App\Mail\RegistrationRejected;
use App\Mail\RegistrationApproved;

class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users|unique:pending_users',
            'password' => 'required|string|min:8',
            'role_id' => 'required|integer',
        ]);

        // Create PENDING USER (NOT in users table yet!)
        $pendingUser = PendingUser::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
        ]);

        // Create ONE notification for all admins (role_id = 1)
        $admins = User::where('role_id', 1)
            ->where('approval_status', 'approved')
            ->get();
        
        if ($admins->count() > 0) {
            // Create notification data
            $notificationData = [
                'type' => 'pending_registration',
                'title' => 'Nouvelle demande d\'inscription',
                'message' => "{$pendingUser->prenom} {$pendingUser->nom} a demandé l'accès au système",
                'metadata' => [
                    'pending_user_id' => $pendingUser->id,
                    'pending_user_email' => $pendingUser->email,
                    'pending_user_name' => "{$pendingUser->prenom} {$pendingUser->nom}",
                ],
                'priority' => 'high',
                'is_read' => false,
            ];

            // Create notification for each admin
            foreach ($admins as $admin) {
                Notification::create(array_merge($notificationData, ['user_id' => $admin->id]));
            }
        }

        // Return success message WITHOUT token
        return response()->json([
            'message' => 'Votre demande d\'inscription a été soumise. Un administrateur examinera votre demande sous peu.',
            'user' => [
                'nom' => $pendingUser->nom,
                'prenom' => $pendingUser->prenom,
                'email' => $pendingUser->email,
            ]
        ], 201);
    }

    // APPROVE USER
    public function approveUser(Request $request, $id)
    {
        $admin = $request->user();
        
        if ($admin->role_id !== 1) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $pendingUser = PendingUser::find($id);
        
        if (!$pendingUser) {
            return response()->json([
                'message' => 'Utilisateur déjà approuvé ou introuvable',
                'error' => 'Cette demande d\'inscription a déjà été traitée.'
            ], 404);
        }

        // Create actual user in users table
        $user = User::create([
            'nom' => $pendingUser->nom,
            'prenom' => $pendingUser->prenom,
            'email' => $pendingUser->email,
            'password' => $pendingUser->password, // Already hashed
            'role_id' => $pendingUser->role_id,
            'approval_status' => 'approved',
            'approval_date' => now(),
            'approved_by' => $admin->id,
        ]);

        // Send approval email to user
        try {
            Mail::to($user->email)->send(new RegistrationApproved($user));
        } catch (\Exception $e) {
            // Log error but continue
        }

        // Delete from pending_users table
        $pendingUser->delete();

        // Mark related notifications as read for ALL admins
        Notification::where('type', 'pending_registration')
            ->whereJsonContains('metadata->pending_user_id', (string)$id)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json([
            'message' => 'Utilisateur approuvé avec succès',
            'user' => $user
        ]);
    }

    // REJECT USER
    public function rejectUser(Request $request, $id)
    {
        $admin = $request->user();
        
        if ($admin->role_id !== 1) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $pendingUser = PendingUser::find($id);
        
        if (!$pendingUser) {
            return response()->json([
                'message' => 'Utilisateur déjà traité ou introuvable',
                'error' => 'Cette demande d\'inscription a déjà été traitée.'
            ], 404);
        }

        // Store email for sending rejection email
        $email = $pendingUser->email;
        $name = "{$pendingUser->prenom} {$pendingUser->nom}";

        // Send rejection email
        try {
            // Create a temporary object for the email
            $rejectedUser = (object)[
                'email' => $email,
                'prenom' => $pendingUser->prenom,
                'nom' => $pendingUser->nom,
            ];
            Mail::to($email)->send(new RegistrationRejected($rejectedUser));
        } catch (\Exception $e) {
            // Log error but continue
        }

        // Mark related notifications as read for ALL admins
        Notification::where('type', 'pending_registration')
            ->whereJsonContains('metadata->pending_user_id', (string)$id)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        // Delete from pending_users table
        $pendingUser->delete();

        return response()->json([
            'message' => 'Demande rejetée et utilisateur notifié par email'
        ]);
    }

    // LOGIN
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Check approval status
        if ($user->approval_status === 'pending') {
            return response()->json(['message' => 'Votre compte est en attente d\'approbation'], 403);
        }

        if ($user->approval_status === 'rejected') {
            return response()->json(['message' => 'Votre demande d\'inscription a été refusée'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);

}


    // LOGOUT
    public function logout(Request $request)
    {
        $user = $request->user();
        $token = $user->currentAccessToken();
        if ($token) {
            $user->tokens()->where('id', $token->id)->delete();
        }

        return response()->json(['message' => 'Déconnexion réussie']);
    }

    // Factorized redirect logic
    private function redirectPath($role)
    {
        switch ($role) {
            case 'admin':
                return '/admindashboard';
            case 'agent':
                return '/agentdashboard';
            case 'technicien':
                return '/techniciendashboard';
            default:
                return '/';
        }
    }
}
