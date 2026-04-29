<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RoomPhoto;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomPhotoController extends Controller
{
    public function store(Request $request, $room_id)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png|max:5120',
            'is_primary' => 'boolean',
        ]);
        $room = Room::findOrFail($room_id);
        $user = $request->user();
        if ($room->kost->owner_id !== $user->id) return response()->json(['message' => 'Forbidden'], 403);
        $path = $request->file('photo')->store('room_photos', 'public');
        $photoUrl = Storage::url($path);
        $roomPhoto = RoomPhoto::create([
            'room_id' => $room_id,
            'photo_url' => $photoUrl,
            'is_primary' => $request->is_primary ?? false,
        ]);
        return response()->json($roomPhoto, 201);
    }

    public function destroy($id)
    {
        $photo = RoomPhoto::findOrFail($id);
        $user = auth()->user();
        if ($photo->room->kost->owner_id !== $user->id) return response()->json(['message' => 'Forbidden'], 403);
        Storage::disk('public')->delete(str_replace('/storage/', '', $photo->photo_url));
        $photo->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
