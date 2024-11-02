<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('setup_tags', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('FCSKID')->unique();
            $table->string('FCCODE')
                ->nullable();
            $table->string('FCSNAME')
                ->nullable();
            $table->string('FCNAME')
                ->nullable();
            $table->string('packing_name')
                ->nullable();
            $table->string('packing_qty')
                ->nullable();
            $table->string('image')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setup_tags');
    }
};
