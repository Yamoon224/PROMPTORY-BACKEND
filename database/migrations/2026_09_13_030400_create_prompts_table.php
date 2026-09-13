<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Prompt mis en vente ou partage gratuitement par un createur.
 *
 * `slug` est ajoute au schema du cahier des charges pour donner au prompt une
 * URL publique stable, independante de son titre (voir CityService::withSlug
 * cote reference : il ne se regenere jamais tout seul au renommage).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prompts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('folder_id')->nullable()->constrained('folders')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->decimal('price', 8, 2)->default(0);
            $table->string('status')->default('draft');
            // Renseigne a la moderation : trace qui a valide/rejete et quand,
            // sans quoi le journal d'activite serait la seule preuve.
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('downloads_count')->default(0);
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prompts');
    }
};
