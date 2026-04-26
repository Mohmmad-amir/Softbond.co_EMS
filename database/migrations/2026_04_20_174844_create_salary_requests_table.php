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
        Schema::create('salary_requests', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('employee_id')->index('employee_id');
            $table->decimal('amount', 10);
            $table->string('month', 20)->nullable();
            $table->text('note')->nullable();
            $table->enum('status', ['pending', 'approved', 'denied'])->nullable()->default('pending');
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('actioned_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_requests');
    }
};
