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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estimate_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->constrained();
            $table->string('number')->unique(); // COM-2025-001
            $table->string('status')->default('pending'); // pending, working, completed, cancelled
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
