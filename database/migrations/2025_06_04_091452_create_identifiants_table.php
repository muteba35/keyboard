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
         Schema::create('identifiants', function (Blueprint $table) {
            $table->id();
            $table->string('nom_utilisateur');
            $table->string('mot_de_passe');
            $table->string('service');
            $table->string('description');
            $table->string('url_service')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('dossier_id')->nullable();
            $table->timestamps();

            // Contraintes
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('dossier_id')->references('id')->on('dossiers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('identifiants');
    }
};
