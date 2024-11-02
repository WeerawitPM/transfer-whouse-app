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
        Schema::create('job_to_tags', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('image')->nullable();
            $table->string('kanban')->nullable();
            $table->string('part_no')->nullable();
            $table->string('part_code')->nullable();
            $table->string('part_name')->nullable();
            $table->string('model')->nullable();
            $table->integer('qty')->nullable();
            $table->string('packing_name')->nullable();
            $table->string('whouse')->nullable();
            $table->string('from_whs')->nullable();
            $table->string('to_whs')->nullable();
            $table->string('qr_code')->nullable();
            $table->integer('status')->nullable();
            $table->string('remark')->nullable();
            $table->foreignId('job_id')
                ->nullable()
                ->constrained('job_heads')
                ->nullOnDelete();
            $table->date('created_date')->nullable();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_to_tags');
    }
};
