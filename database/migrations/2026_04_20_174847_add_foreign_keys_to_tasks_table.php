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
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign(['project_id'], 'tasks_ibfk_1')->references(['id'])->on('projects')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['assigned_to'], 'tasks_ibfk_2')->references(['id'])->on('employees')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign('tasks_ibfk_1');
            $table->dropForeign('tasks_ibfk_2');
        });
    }
};
