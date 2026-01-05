<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sewas', function (Blueprint $table) {
            $table->id();
            $table->string('kode_sewa')->nullable();
            $table->foreignId('penyewa_id')->constrained('penyewas')->onDelete('cascade');
            $table->text('kostum_id'); // simpan banyak id dalam JSON
            $table->date('tanggal_sewa');
            $table->date('tanggal_kembali');
            $table->integer('total_biaya');
            $table->text('catatan')->nullable();
            $table->boolean('status')->default(false);
            $table->boolean('status_bayar')->default(false);
            $table->string('metode_pembayaran')->nullable();
            $table->string('no_rekening')->nullable();
            $table->integer('denda')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sewas');
    }
};
