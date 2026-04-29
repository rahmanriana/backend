<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kost_facilities', function (Blueprint $table) {
            $table->foreignId('kost_id')->constrained('kosts')->onDelete('cascade');
            $table->foreignId('facility_id')->constrained('facilities')->onDelete('cascade');
            $table->primary(['kost_id', 'facility_id']);
        });
        Schema::create('room_facilities', function (Blueprint $table) {
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('facility_id')->constrained('facilities')->onDelete('cascade');
            $table->primary(['room_id', 'facility_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('kost_facilities');
        Schema::dropIfExists('room_facilities');
    }
};