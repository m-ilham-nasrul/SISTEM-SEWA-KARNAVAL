<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('sewas', function (Blueprint $table) {
            $table->string('nama_bank')->nullable();
            $table->string('nama_ewallet')->nullable();
            $table->string('nomor_ewallet')->nullable();
        });
    }

    public function down()
    {
        Schema::table('sewas', function (Blueprint $table) {
            $table->dropColumn(['nama_bank', 'nama_ewallet', 'nomor_ewallet']);
        });
    }
};
