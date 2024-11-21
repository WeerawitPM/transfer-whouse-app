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
        Schema::create('job_heads', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('job_no')->nullable();
            $table->string('doc_no')->nullable();
            $table->string('doc_ref_no')->nullable();
            $table->string('department')->nullable();
            $table->string('from_whs')->nullable();
            $table->string('to_whs')->nullable();
            $table->integer('status')->nullable();
            $table->string('remark')->nullable();
            $table->date('created_date')->nullable();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('job_master')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_heads');
    }
};
