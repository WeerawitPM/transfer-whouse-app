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
        Schema::table('transfer_books', function (Blueprint $table) {
            $table->boolean('is_menu_scan')->default(false);
            $table->boolean('is_menu_manual')->default(false);
            $table->boolean('is_menu_detail')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfer_books', function (Blueprint $table) {
            //
        });
    }
};
