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
        Schema::table('salary_requests', function (Blueprint $table) {
            $table->foreign(['employee_id'], 'salary_requests_ibfk_1')->references(['id'])->on('employees')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_requests', function (Blueprint $table) {
            $table->dropForeign('salary_requests_ibfk_1');
        });
    }
};
