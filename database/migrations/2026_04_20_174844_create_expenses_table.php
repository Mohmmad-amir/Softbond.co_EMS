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
        Schema::create('expenses', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('project_id')->nullable()->index('project_id');
            $table->string('description', 200);
            $table->enum('category', ['Salary', 'Software', 'Hosting', 'Operations', 'Tools', 'Marketing', 'Other']);
            $table->decimal('amount', 10);
            $table->date('date');
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
