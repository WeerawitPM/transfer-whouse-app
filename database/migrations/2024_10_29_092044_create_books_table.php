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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('FCSKID');
            $table->string('FCREFTYPE')
                ->nullable();
            $table->string('FCCORP')
                ->nullable();
            $table->string('FCBRANCH')
                ->nullable();
            $table->string('FCCODE')
                ->nullable();
            $table->string('FCNAME')
                ->nullable();
            $table->string('FCNAME2')
                ->nullable();
            $table->string('FCACCBOOK')
                ->nullable();
            $table->foreignId('from_whs_id')
                ->nullable()
                ->constrained('whouses')
                ->nullOnDelete();
            $table->foreignId('to_whs_id')
                ->nullable()
                ->constrained('whouses')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
