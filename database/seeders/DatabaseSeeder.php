<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\IaModel;
use App\Models\Prompt;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $admin = User::factory()->create([
            'name' => 'Administratrice Promptory',
            'email' => 'admin@promptory.test',
            'role' => 'admin',
        ]);
        $admin->assignRole('admin');

        $creator = User::factory()->create([
            'name' => 'Camille Createur',
            'email' => 'creator@promptory.test',
        ]);
        $creator->assignRole('user');

        $buyer = User::factory()->create([
            'name' => 'Bruno Acheteur',
            'email' => 'buyer@promptory.test',
        ]);
        $buyer->assignRole('user');

        $categories = collect(['Ecriture', 'Marketing', 'Developpement', 'Image & design'])
            ->map(fn (string $name) => Category::create(['name' => $name, 'slug' => \Illuminate\Support\Str::slug($name)]));

        $tags = collect(['SEO', 'Email', 'Landing page', 'Refactoring', 'Debug'])
            ->map(fn (string $name) => Tag::create(['name' => $name, 'slug' => \Illuminate\Support\Str::slug($name)]));

        $iaModels = collect(['ChatGPT', 'Claude', 'Midjourney', 'Gemini'])
            ->map(fn (string $name) => IaModel::create(['name' => $name, 'slug' => \Illuminate\Support\Str::slug($name), 'is_active' => true]));

        $prompt = Prompt::create([
            'user_id' => $creator->id,
            'title' => 'Redacteur de fiches produit e-commerce',
            'slug' => 'redacteur-fiches-produit-e-commerce',
            'content' => "Tu es un redacteur e-commerce senior. Ecris une fiche produit de 150 mots pour : {produit}. Ton : {ton}. Inclus 3 puces de benefices et un appel a l'action.",
            'price' => 4.99,
            'status' => 'published',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);
        $prompt->categories()->attach($categories->take(2)->pluck('id'));
        $prompt->tags()->attach($tags->take(2)->pluck('id'));
        $prompt->iaModels()->attach($iaModels->take(1)->pluck('id'));

        Prompt::create([
            'user_id' => $creator->id,
            'title' => 'Debuggeur de code pas a pas',
            'slug' => 'debuggeur-de-code-pas-a-pas',
            'content' => "Tu es un ingenieur logiciel senior. Analyse ce message d'erreur et ce code : {erreur} {code}. Explique la cause racine puis propose un correctif minimal.",
            'price' => 0,
            'status' => 'published',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ])->categories()->attach($categories->last()->id);
    }
}
