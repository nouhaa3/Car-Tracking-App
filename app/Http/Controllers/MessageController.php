<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        // Validate the form data
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'message' => 'required|string',
        ]);

        // Insert into your SQL table
        DB::table('contact_messages')->insert([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'message' => $validated['message'],
            'is_read' => false,
            'created_at' => now(),
        ]);

        return response()->json(['status' => 'success']);
    }
}
