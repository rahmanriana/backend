<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Users
        $owners = \App\Models\User::factory()->count(3)->create(['role' => 'owner']);
        $seekers = \App\Models\User::factory()->count(2)->create(['role' => 'seeker']);

        // Facilities
        $facilityNames = ['WiFi', 'AC', 'Kamar Mandi Dalam', 'Kasur', 'Lemari', 'Meja Belajar', 'Parkir Motor', 'Dapur', 'Laundry', 'CCTV'];
        $facilities = collect($facilityNames)->map(fn($name) => \App\Models\Facility::create(['name' => $name]));

        // Kosts
        $cities = [
            ['city' => 'Jakarta', 'province' => 'DKI Jakarta', 'lat' => -6.2, 'lng' => 106.8167],
            ['city' => 'Bandung', 'province' => 'Jawa Barat', 'lat' => -6.9147, 'lng' => 107.6098],
            ['city' => 'Surabaya', 'province' => 'Jawa Timur', 'lat' => -7.2575, 'lng' => 112.7521],
            ['city' => 'Yogyakarta', 'province' => 'DI Yogyakarta', 'lat' => -7.7956, 'lng' => 110.3695],
            ['city' => 'Semarang', 'province' => 'Jawa Tengah', 'lat' => -6.9667, 'lng' => 110.4167],
            ['city' => 'Denpasar', 'province' => 'Bali', 'lat' => -8.65, 'lng' => 115.2167],
            ['city' => 'Medan', 'province' => 'Sumatera Utara', 'lat' => 3.5952, 'lng' => 98.6722],
            ['city' => 'Makassar', 'province' => 'Sulawesi Selatan', 'lat' => -5.1477, 'lng' => 119.4327],
        ];
        $kosts = collect($cities)->map(function($city, $i) use ($owners) {
            return \App\Models\Kost::create([
                'owner_id' => $owners[$i % $owners->count()]->id,
                'name' => 'Kost ' . $city['city'],
                'description' => 'Deskripsi Kost di ' . $city['city'],
                'address' => 'Jl. Contoh No. ' . ($i+1),
                'city' => $city['city'],
                'province' => $city['province'],
                'latitude' => $city['lat'],
                'longitude' => $city['lng'],
                'type' => ['putra','putri','campur'][$i%3],
            ]);
        });

        // Rooms
        $rooms = collect();
        foreach ($kosts as $kost) {
            for ($i=0; $i<3; $i++) {
                $rooms->push(\App\Models\Room::create([
                    'kost_id' => $kost->id,
                    'name' => 'Kamar ' . ($i+1) . ' ' . $kost->name,
                    'price' => rand(500, 3000) * 1000,
                    'is_available' => true,
                    'size' => rand(9, 20),
                    'capacity' => rand(1, 3),
                    'description' => 'Kamar nyaman di ' . $kost->name,
                ]));
            }
        }
        $rooms = $rooms->take(20);

        // Room Photos
        foreach ($rooms as $room) {
            for ($i=0; $i<2; $i++) {
                \App\Models\RoomPhoto::create([
                    'room_id' => $room->id,
                    'photo_url' => '/storage/room_photos/dummy'.rand(1,10).'.jpg',
                    'is_primary' => $i==0,
                ]);
            }
        }

        // Kost Facilities & Room Facilities
        foreach ($kosts as $kost) {
            $kost->facilities()->sync($facilities->random(rand(3,6))->pluck('id')->toArray());
        }
        foreach ($rooms as $room) {
            $room->facilities()->sync($facilities->random(rand(2,5))->pluck('id')->toArray());
        }

        // Favorites
        foreach ($seekers as $seeker) {
            $favKosts = $kosts->random(rand(2,4));
            foreach ($favKosts as $kost) {
                \App\Models\Favorite::create([
                    'user_id' => $seeker->id,
                    'kost_id' => $kost->id,
                ]);
            }
        }
    }
}
