<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function __construct(
        protected MailService $mailService
    ) {}
    // ─────────────────────────────────────────────────
    // POST /api/contact (public)
    // ─────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation échouée',
                'errors'  => $validator->errors()
            ], 422);
        }

        $contact = Contact::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'subject'    => $request->subject,
            'message'    => $request->message,
            'ip_address' => $request->ip(),
            'status'     => 'pending',
            'is_read'    => false,
        ]);

        // Optionnel: Envoyer un email de confirmation
        // Mail::to($request->email)->send(new ContactConfirmation($contact));

        return response()->json([
            'message' => 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.',
            'contact' => $contact
        ], 201);
    }

    // ─────────────────────────────────────────────────
    // GET /api/admin/contacts (admin only)
    // ─────────────────────────────────────────────────
    public function index()
    {
        $contacts = Contact::orderBy('created_at', 'desc')->get();

        return response()->json([
            'contacts' => $contacts,
            'unread_count' => Contact::where('is_read', false)->count()
        ]);
    }

    // ─────────────────────────────────────────────────
    // GET /api/admin/contacts/{id} (admin only)
    // ─────────────────────────────────────────────────
    public function show($id)
    {
        $contact = Contact::findOrFail($id);

        // Marquer comme lu si pas déjà fait
        if (!$contact->is_read) {
            $contact->markAsRead();
        }

        return response()->json($contact);
    }

    // ─────────────────────────────────────────────────
    // DELETE /api/admin/contacts/{id} (admin only)
    // ─────────────────────────────────────────────────
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return response()->json(['message' => 'Message supprimé avec succès']);
    }

    // ─────────────────────────────────────────────────
    // PUT /api/admin/contacts/{id}/reply (admin only)
    // ─────────────────────────────────────────────────
    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply_message' => 'required|string|min:5',
        ]);

        $contact = Contact::findOrFail($id);

        // 👇 Envoyer l'email de réponse
        try {
            $this->mailService->sendContactReply(
                $contact,
                $request->reply_message
            );
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        // Marquer comme répondu
        $contact->update(['status' => 'replied']);

        return response()->json([
            'message' => 'Réponse envoyée avec succès à ' . $contact->email,
            'contact' => $contact,
        ]);
    }

    // ─────────────────────────────────────────────────
    // GET /api/admin/contacts/unread/count (admin only)
    // ─────────────────────────────────────────────────
    public function unreadCount()
    {
        return response()->json([
            'count' => Contact::where('is_read', false)->count()
        ]);
    }
}
