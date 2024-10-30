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
            $table->string('FCREFTYPE');
            $table->string('FCCORP');
            $table->string('FCBRANCH');
            $table->string('FCCODE');
            $table->string('FCNAME');
            $table->string('FCNAME2');
            $table->string('FCACCBOOK');
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
