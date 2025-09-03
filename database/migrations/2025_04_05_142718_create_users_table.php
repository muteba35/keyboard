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
    $table->string('phone')->nullable();


    $table->timestamp('email_verified_at')->nullable(); // <-- ligne ajoutée pour éviter ton erreur

    $table->string('password');
    $table->string('rememberToken')->nullable();
     $table->boolean('is_blocked')->default(false);

    // Champs pour OTP
    $table->string('otp')->nullable();
    $table->timestamp('otp_expires_at')->nullable();
     $table->timestamp('otp_verified_at')->nullable();

    // Historique de connexions
    $table->string('last_ip')->nullable();
    $table->string('browser')->nullable();
    $table->string('user_agent')->nullable();
  

    // Date du dernier changement de mot de passe
    $table->timestamp('password_changed_at')->nullable();
    $table->timestamp('blocked_until')->nullable();

   //Tentatives de connexion
    $table->unsignedInteger('failed_attempts')->default(0);
    $table->timestamp('last_failed_attempt_at')->nullable();
        
  

    $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
