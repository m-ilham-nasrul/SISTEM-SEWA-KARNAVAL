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

            $table->foreignId('sewa_id')
                ->constrained('sewas')
                ->cascadeOnDelete();

            $table->foreignId('kostum_id')
                ->constrained('kostums')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('harga');
            $table->integer('qty')->default(1);
            $table->unsignedBigInteger('subtotal')->nullable();

            $table->timestamps();

            $table->index('sewa_id');
            $table->index('kostum_id');

           
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_sewas');
    }
};
