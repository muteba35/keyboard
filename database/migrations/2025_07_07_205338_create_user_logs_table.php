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
        Schema::create('user_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Qui a déclenché l’action
            $table->string('action'); // ajout, modification, suppression, etc.
            $table->text('description')->nullable(); // Ce qui s’est passé
            $table->string('ip_address')->nullable(); // IP
            $table->string('user_agent')->nullable(); // Navigateur
            $table->unsignedBigInteger('related_id')->nullable(); // ID de l’élément ciblé
            $table->string('related_type')->nullable(); // Type de l’élément ciblé (Identifiant, Dossier…)
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_logs');
    }
};
