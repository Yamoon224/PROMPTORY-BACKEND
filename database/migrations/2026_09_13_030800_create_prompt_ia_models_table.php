<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Pivot `Prompt_IA` du cahier des charges : outils IA cibles par un prompt. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prompt_ia_models', function (Blueprint $table) {
            $table->foreignId('prompt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ia_model_id')->constrained()->cascadeOnDelete();
            $table->primary(['prompt_id', 'ia_model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prompt_ia_models');
    }
};
