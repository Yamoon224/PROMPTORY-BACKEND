<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Vente d'un prompt ou d'un pack (jamais les deux : contrainte applicative
 * portee par PurchaseService, une contrainte SQL de type XOR sur deux
 * colonnes nullable n'etant pas portable entre moteurs).
 *
 * `client_reference` rend un rejeu d'achat idempotent : un double clic ou une
 * requete reessayee apres coupure reseau ne doit jamais debiter deux fois le
 * meme panier.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('client_reference')->unique();
            $table->foreignId('prompt_id')->nullable()->constrained('prompts')->nullOnDelete();
            $table->foreignId('pack_id')->nullable()->constrained('packs')->nullOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('price', 8, 2);
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('commission', 8, 2);
            $table->string('payment_status')->default('pending');
            $table->string('payment_gateway')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamps();

            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
