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
        Schema::create('ref_types', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('FCSKID');
            $table->string('FCCODE');
            $table->string('FCRETYPE');
            $table->string('FCNAME');
            $table->string('FCNAME2');
            $table->string('FCNGLNAME');
            $table->string('FCREPNAME');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_types');
    }
};
