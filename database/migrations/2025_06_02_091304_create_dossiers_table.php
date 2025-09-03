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
       Schema::create('dossiers', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // Nom du dossier, ex: "Réseaux sociaux"
            $table->unsignedBigInteger('user_id'); // Chaque utilisateur a ses propres dossiers
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossiers');
    }
};
