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
        Schema::create('projects', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 150);
            $table->string('client', 150)->nullable();
            $table->enum('type', ['Web Dev', 'App Dev', 'Game Dev', 'Marketing', 'Design', 'Other']);
            $table->decimal('budget', 12)->nullable()->default(0);
            $table->decimal('received', 12)->nullable()->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['new', 'active', 'on_hold', 'completed', 'cancelled'])->nullable()->default('new');
            $table->text('description')->nullable();
            $table->integer('progress')->nullable()->default(0);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
