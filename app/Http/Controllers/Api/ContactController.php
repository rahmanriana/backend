<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Kost;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'owner') {
            $contacts = Contact::where('owner_id', $user->id)->with('seeker', 'kost')->get();
        } else {
            $contacts = Contact::where('seeker_id', $user->id)->with('owner', 'kost')->get();
        }
        return response()->json($contacts);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'seeker') return response()->json(['message' => 'Forbidden'], 403);
        $validated = $request->validate([
            'owner_id' => 'required|exists:users,id',
            'kost_id' => 'required|exists:kosts,id',
            'message' => 'required|string',
        ]);
        $validated['seeker_id'] = $user->id;
        $validated['status'] = 'pending';
        $contact = Contact::create($validated);
        return response()->json($contact, 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);
        $user = $request->user();
        if ($user->id !== $contact->owner_id && $user->id !== $contact->seeker_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $request->validate(['status' => 'required|in:pending,responded,closed']);
        $contact->status = $request->status;
        $contact->save();
        return response()->json($contact);
    }
}
