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
            $table->string('fullname');
            $table->string('username')->unique();
            $table->string('email')->unique(); // Kita tetap simpan email, karena standar Laravel
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('level'); // (Admin, Master Divisi, Master User)
            $table->unsignedBigInteger('division_id')->nullable(); // FK ke tabel divisions
            $table->string('status')->default('1');
            $table->timestamp('last_login')->nullable();
            $table->rememberToken();
            $table->timestamps(); // Ini akan membuat kolom created_at dan updated_at
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
