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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role')->default('staff')->index();
            $table->string('account_status')->default('invited')->index();
            $table->string('gender')->nullable();
            $table->date('birth_date')->nullable();
            $table->boolean('is_student')->default(false);
            $table->string('nearest_station')->nullable();
            $table->string('postal_code', 12)->nullable();
            $table->text('address')->nullable();
            $table->string('building')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('secondary_email')->nullable();
            $table->string('notification_method')->default('email');
            $table->string('line_user_id')->nullable()->unique();
            $table->timestamp('line_linked_at')->nullable();
            $table->boolean('has_car')->default(false);
            $table->string('car_number')->nullable();
            $table->unsignedInteger('base_fee')->default(0);
            $table->text('bank_details')->nullable();
            $table->string('contract_path')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->index(['role', 'account_status']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
