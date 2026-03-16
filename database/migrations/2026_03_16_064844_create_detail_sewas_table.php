<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_sewas', function (Blueprint $table) {

            $table->id();

            // relasi ke tabel sewas
            $table->foreignId('sewa_id')
                ->constrained('sewas')
                ->cascadeOnDelete();

            // relasi ke tabel kostums
            $table->foreignId('kostum_id')
                ->constrained('kostums')
                ->cascadeOnDelete();

            // harga kostum saat disewa
            $table->integer('harga')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_sewas');
    }
};