<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Kost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    public function index($kost_id)
    {
        $rooms = Room::where('kost_id', $kost_id)->with(['photos', 'facilities'])->get();
        return response()->json($rooms);
    }

    public function store(Request $request, $kost_id)
    {
        $user = $request->user();
        $kost = Kost::findOrFail($kost_id);
        if ($kost->owner_id !== $user->id) return response()->json(['message' => 'Forbidden'], 403);
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'is_available' => 'boolean',
            'size' => 'nullable|integer',
            'capacity' => 'integer',
            'description' => 'required|string',
        ]);
        $validated['kost_id'] = $kost_id;
        $room = Room::create($validated);
        return response()->json($room, 201);
    }

    public function show($id)
    {
        $room = Room::with(['photos', 'facilities'])->findOrFail($id);
        return response()->json($room);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $room = Room::findOrFail($id);
        $kost = $room->kost;
        if ($kost->owner_id !== $user->id) return response()->json(['message' => 'Forbidden'], 403);
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'is_available' => 'sometimes|boolean',
            'size' => 'nullable|integer',
            'capacity' => 'sometimes|integer',
            'description' => 'sometimes|string',
        ]);
        $room->update($validated);
        return response()->json($room);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $room = Room::findOrFail($id);
        $kost = $room->kost;
        if ($kost->owner_id !== $user->id) return response()->json(['message' => 'Forbidden'], 403);
        $room->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
