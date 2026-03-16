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

            // kode transaksi
            $table->string('kode_sewa')->nullable();

            // relasi penyewa
            $table->foreignId('penyewa_id')
                ->constrained('penyewas')
                ->cascadeOnDelete();

            // menyimpan banyak kostum dalam JSON
            $table->text('kostum_id');

            // tanggal penyewaan
            $table->date('tanggal_sewa');
            $table->date('tanggal_kembali');

            // biaya sewa
            $table->integer('total_biaya');

            // denda jika ada keterlambatan / kerusakan
            $table->integer('denda')->default(0);

            $table->text('catatan')->nullable();

            /*
            STATUS SEWA
            0 = disewa
            1 = dikembalikan
            2 = menunggu pembayaran
            3 = selesai
            */
            $table->tinyInteger('status')->default(0);

            /*
            STATUS PEMBAYARAN
            */
            $table->boolean('status_bayar')->default(false);

            /*
            DATA MIDTRANS
            */
            $table->string('midtrans_order_id')->nullable();
            $table->text('snap_token')->nullable();
            $table->string('transaction_status')->nullable();
            $table->string('payment_type')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sewas');
    }
};