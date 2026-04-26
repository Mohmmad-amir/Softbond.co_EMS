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
        Schema::create('employees', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->nullable()->index('user_id');
            $table->string('name', 100);
            $table->string('email', 100)->unique('email');
            $table->string('phone', 20)->nullable();
            $table->enum('department', ['Web Dev', 'App Dev', 'Game Dev', 'Marketing', 'Design', 'Management']);
            $table->string('designation', 100)->nullable();
            $table->decimal('salary', 10)->nullable()->default(0);
            $table->date('join_date')->nullable();
            $table->string('nid', 50)->nullable();
            $table->text('address')->nullable();
            $table->enum('status', ['active', 'on_leave', 'inactive'])->nullable()->default('active');
            $table->enum('payment_method', ['bank', 'bkash', 'nagad', 'rocket', 'cash'])->nullable()->default('cash');
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account', 100)->nullable();
            $table->string('mobile_banking_number', 20)->nullable();
            $table->string('photo')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
