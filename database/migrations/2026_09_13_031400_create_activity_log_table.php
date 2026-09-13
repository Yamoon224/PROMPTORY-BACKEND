<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Journal d'activite au sens du cahier des charges (analytics produit), a ne
 * pas confondre avec un journal d'audit de securite : il trace des
 * evenements d'usage (vue, achat, telechargement…), pas des changements de
 * ligne. Ecriture seule depuis le domaine ; jamais mis a jour.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('prompt_id')->nullable()->constrained('prompts')->nullOnDelete();
            $table->string('action');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};
