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

            $table->integer('dp')->default(0);
            $table->integer('sisa_bayar')->default(0);

            $table->unsignedBigInteger('total_biaya')->default(0);
            $table->unsignedBigInteger('denda')->default(0);

            $table->enum('kondisi', ['baik', 'rusak'])->nullable();
            $table->text('catatan')->nullable();

            $table->tinyInteger('status')->default(0);
            $table->enum('status_bayar', ['pending', 'dp_paid', 'paid'])->default('pending');

            $table->string('midtrans_order_id_dp')->nullable();
            $table->string('midtrans_order_id_pelunasan')->nullable();
            $table->text('snap_token')->nullable();
            $table->timestamp('snap_token_created_at')->nullable();
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
