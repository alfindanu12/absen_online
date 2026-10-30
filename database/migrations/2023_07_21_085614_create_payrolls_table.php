<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('bulan');
            $table->string('tahun');
            $table->unsignedBigInteger('status_id');
            $table->foreign('status_id')->references('id')->on('status_ptkps');
            $table->decimal('gaji', 15, 2)->default(0);
            $table->decimal('pot_tunjangan_makan', 15, 2)->default(0);
            $table->decimal('pot_tunjangan_transport', 15, 2)->default(0);
            $table->decimal('setoran_bpjs_kes', 15, 2)->default(0);
            $table->decimal('tunjangan_bpjs_kes', 15, 2)->default(0);
            $table->decimal('setoran_bpjs_tk', 15, 2)->default(0);
            $table->decimal('tunjangan_bpjs_tk', 15, 2)->default(0);
            $table->decimal('tunjangan_pensiun', 15, 2)->default(0);
            $table->decimal('tunjangan_komunikasi', 15, 2)->default(0);
            $table->decimal('tunjangan_pph_21', 15, 2)->default(0);
            $table->decimal('pot_lainnya', 15, 2)->default(0);
            $table->decimal('lembur', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payrolls');
    }
}
