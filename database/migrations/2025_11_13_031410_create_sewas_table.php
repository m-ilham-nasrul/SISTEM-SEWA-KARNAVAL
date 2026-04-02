<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sewas', function (Blueprint $table) {

            $table->id();

            $table->string('kode_sewa')->nullable()->unique();

            $table->foreignId('penyewa_id')
                ->constrained('penyewas')
                ->cascadeOnDelete();

            $table->date('tanggal_sewa');
            $table->date('tanggal_kembali');

            $table->unsignedBigInteger('total_biaya')->default(0);
            $table->unsignedBigInteger('denda')->default(0);

            $table->enum('kondisi', ['baik', 'rusak'])->nullable();
            $table->text('catatan')->nullable();

            $table->tinyInteger('status')->default(0);
            $table->boolean('status_bayar')->default(false);

            $table->string('midtrans_order_id')->nullable()->unique();
            $table->text('snap_token')->nullable();
            $table->string('transaction_status')->nullable();
            $table->string('payment_type')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('status_bayar');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sewas');
    }
};
