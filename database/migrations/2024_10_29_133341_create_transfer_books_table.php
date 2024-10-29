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
        Schema::create('transfer_books', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('transfer_ref_type_id')
                ->nullable()
                ->constrained('transfer_ref_types')
                ->nullOnDelete();
            $table->foreignId('book_id')
                ->nullable()
                ->constrained('books')
                ->nullOnDelete();
            $table->boolean('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_books');
    }
};
