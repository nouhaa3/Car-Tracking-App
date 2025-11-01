<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    /**
     * Send password reset link to email
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Check if user exists
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'message' => 'Aucun compte trouvé avec cet email.'
            ], 404);
        }

        // Generate token
        $token = Str::random(64);

        // Store token in database
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // For development: return the reset link directly
        // In production, you should send this via email
        // Get the frontend URL from request origin or use default
        $frontendUrl = $request->header('Origin', 'http://localhost:5173');
        $resetUrl = $frontendUrl . '/reset-password?token=' . $token . '&email=' . urlencode($request->email);

        return response()->json([
            'message' => 'Un lien de réinitialisation a été généré.',
            'reset_url' => $resetUrl,
            'token' => $token,
            'email' => $request->email,
            'note' => 'En production, ce lien sera envoyé par email. Pour le développement, copiez ce lien.'
        ], 200);
    }

    /**
     * Reset password with token
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Find the password reset record
        $passwordReset = \DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset) {
            return response()->json([
                'message' => 'Le lien de réinitialisation est invalide.'
            ], 400);
        }

        // Check if token matches
        if (!Hash::check($request->token, $passwordReset->token)) {
            return response()->json([
                'message' => 'Le token est invalide.'
            ], 400);
        }

        // Check if token is expired (60 minutes)
        $createdAt = \Carbon\Carbon::parse($passwordReset->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            return response()->json([
                'message' => 'Le lien de réinitialisation a expiré.'
            ], 400);
        }

        // Update user password
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur introuvable.'
            ], 404);
        }

        $user->password = Hash::make($request->password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Delete the password reset record
        \DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'message' => 'Votre mot de passe a été réinitialisé avec succès.'
        ], 200);
    }
}
