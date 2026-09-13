<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Referentiel des outils IA cibles par un prompt (ChatGPT, Midjourney, Claude…).
 *
 * Partage entre tous les createurs : deux prompts pour le meme outil pointent
 * la meme fiche, sans quoi un filtre « prompts pour ChatGPT » n'en trouverait
 * qu'une partie.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ia_models', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ia_models');
    }
};
