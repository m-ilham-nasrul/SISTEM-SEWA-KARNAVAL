<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kostums', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kostum');
            $table->string('image_kostum')->nullable();
            $table->string('kategori');
            $table->text('catatan')->nullable();
            $table->integer('harga');
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kostums');
    }
};
