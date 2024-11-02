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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->integer('emp_id')->nullable();
            $table->string('image')->nullable();
            $table->foreignId('corp_id')
                ->nullable()
                ->constrained('corps')
                ->nullOnDelete();
            $table->foreignId('dept_id')
                ->nullable()
                ->constrained('depts')
                ->nullOnDelete();
            $table->foreignId('sect_id')
                ->nullable()
                ->constrained('sects');
            $table->foreignId('emplr_id')
                ->nullable()
                ->constrained('emplrs')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
