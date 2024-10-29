<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('corps', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('FCSKID');
            $table->string('FCCODE');
            $table->string('FCNAME');
            $table->string('FCTAXID');
            $table->string('FCADDR1');
            $table->string('FCADDR2');
            $table->string('FCTEL');
            $table->string('FCFAX');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corps');
    }
};
