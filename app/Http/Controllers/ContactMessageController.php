<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use App\Mail\MessageReplyMail;

class ContactMessageController extends Controller
{
    /**
     * Get all contact messages
     */
    public function index()
    {
        try {
            // Check if table exists
            if (!Schema::hasTable('contact_messages')) {
                return response()->json([
                    'error' => 'Table non trouvée',
                    'message' => 'Veuillez exécuter la migration: php artisan migrate'
                ], 500);
            }

            $messages = DB::table('contact_messages')
                ->leftJoin('users', 'contact_messages.replied_by', '=', 'users.id')
                ->select(
                    'contact_messages.*',
                    'users.nom as admin_nom',
                    'users.prenom as admin_prenom'
                )
                ->orderBy('contact_messages.created_at', 'desc')
                ->get();

            return response()->json($messages);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération des messages',
                'message' => $e->getMessage(),
                'hint' => 'Assurez-vous que la migration a été exécutée: php artisan migrate'
            ], 500);
        }
    }

    /**
     * Store a new contact message (public route)
     */
    public function store(Request $request)
    {
        try {
            // Check if table exists
            if (!Schema::hasTable('contact_messages')) {
                return response()->json([
                    'error' => 'Table non trouvée',
                    'message' => 'Veuillez exécuter la migration: php artisan migrate'
                ], 500);
            }

            // Validate input
            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'message' => 'required|string|max:2000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Insert message
            $messageId = DB::table('contact_messages')->insertGetId([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message,
                'is_read' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Message envoyé avec succès',
                'id' => $messageId
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de l\'envoi du message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a single message by ID
     */
    public function show($id)
    {
        try {
            $message = DB::table('contact_messages')
                ->leftJoin('users', 'contact_messages.replied_by', '=', 'users.id')
                ->select(
                    'contact_messages.*',
                    'users.nom as admin_nom',
                    'users.prenom as admin_prenom'
                )
                ->where('contact_messages.id', $id)
                ->first();

            if (!$message) {
                return response()->json(['error' => 'Message non trouvé'], 404);
            }

            return response()->json($message);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération du message',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark message as read
     */
    public function markAsRead($id)
    {
        try {
            $updated = DB::table('contact_messages')
                ->where('id', $id)
                ->update(['is_read' => 1]);

            if (!$updated) {
                return response()->json(['error' => 'Message non trouvé'], 404);
            }

            return response()->json(['success' => true, 'message' => 'Message marqué comme lu']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la mise à jour',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reply to a contact message and send email
     */
    public function reply(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reply' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation échouée',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            // Get the contact message
            $contactMessage = DB::table('contact_messages')->where('id', $id)->first();

            if (!$contactMessage) {
                return response()->json(['error' => 'Message non trouvé'], 404);
            }

            // Get authenticated user
            $user = $request->user();

            // Update the message with reply
            DB::table('contact_messages')
                ->where('id', $id)
                ->update([
                    'admin_reply' => $request->reply,
                    'replied_at' => now(),
                    'replied_by' => $user->id,
                    'is_read' => 1,
                ]);

            // Send email to the person who sent the message
            try {
                Mail::to($contactMessage->email)->send(
                    new MessageReplyMail(
                        $contactMessage,
                        $request->reply,
                        $user->nom . ' ' . $user->prenom
                    )
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Réponse envoyée avec succès par email'
                ]);
            } catch (\Exception $e) {
                // Reply saved but email failed
                return response()->json([
                    'success' => true,
                    'message' => 'Réponse enregistrée mais l\'email n\'a pas pu être envoyé',
                    'email_error' => $e->getMessage()
                ], 206); // 206 Partial Content
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de l\'envoi de la réponse',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a contact message
     */
    public function destroy($id)
    {
        try {
            $deleted = DB::table('contact_messages')->where('id', $id)->delete();

            if (!$deleted) {
                return response()->json(['error' => 'Message non trouvé'], 404);
            }

            return response()->json(['success' => true, 'message' => 'Message supprimé']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la suppression',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unread messages count
     */
    public function unreadCount()
    {
        try {
            $count = DB::table('contact_messages')
                ->where('is_read', false)
                ->count();

            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['count' => 0]);
        }
    }
}
