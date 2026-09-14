<?php

namespace Database\Seeders;

use App\Domains\Packs\Enums\PackStatus;
use App\Domains\Prompts\Enums\PromptStatus;
use App\Models\Category;
use App\Models\IaModel;
use App\Models\Pack;
use App\Models\Prompt;
use App\Models\Review;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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

        $moderator = User::factory()->create([
            'name' => 'Moderateur Promptory',
            'email' => 'moderateur@promptory.test',
        ]);
        $moderator->assignRole('moderator');

        // Un createur nomme, pour se connecter facilement en demo, plus un
        // groupe de createurs generes pour peupler la marketplace.
        $mainCreator = User::factory()->create([
            'name' => 'Camille Createur',
            'email' => 'creator@promptory.test',
        ]);
        $mainCreator->assignRole('user');

        $creators = User::factory()
            ->count(19)
            ->create()
            ->each(fn (User $user) => $user->assignRole('user'))
            ->prepend($mainCreator);

        $mainBuyer = User::factory()->create([
            'name' => 'Bruno Acheteur',
            'email' => 'buyer@promptory.test',
        ]);
        $mainBuyer->assignRole('user');

        $buyers = User::factory()
            ->count(14)
            ->create()
            ->each(fn (User $user) => $user->assignRole('user'))
            ->prepend($mainBuyer);

        $categories = collect([
            'Ecriture', 'Marketing', 'Developpement', 'Image & design', 'Business',
            'Education', 'Sante & bien-etre', 'Productivite',
        ])->map(fn (string $name) => Category::create(['name' => $name, 'slug' => Str::slug($name)]));

        $tags = collect([
            'SEO', 'Email', 'Landing page', 'Refactoring', 'Debug', 'Reseaux sociaux',
            'Copywriting', 'Analyse de donnees', 'Prompt engineering', 'Automatisation',
            'Storytelling', 'B2B', 'B2C', 'Freelance', 'Startup',
        ])->map(fn (string $name) => Tag::create(['name' => $name, 'slug' => Str::slug($name)]));

        $iaModels = collect([
            'ChatGPT', 'Claude', 'Midjourney', 'Gemini', 'DALL-E', 'Stable Diffusion',
        ])->map(fn (string $name) => IaModel::create(['name' => $name, 'slug' => Str::slug($name), 'is_active' => true]));

        // --- Prompts (200+) ---------------------------------------------------

        /** @var Collection<int, Prompt> $prompts */
        $prompts = Prompt::factory()
            ->count(210)
            ->sequence(fn () => ['user_id' => $creators->random()->id])
            ->create();

        $prompts->each(function (Prompt $prompt) use ($categories, $tags, $iaModels, $admin) {
            $prompt->categories()->attach($categories->random(random_int(1, 2))->pluck('id')->all());
            $prompt->tags()->attach($tags->random(random_int(1, 3))->pluck('id')->all());
            $prompt->iaModels()->attach($iaModels->random(random_int(1, 2))->pluck('id')->all());

            if ($prompt->status === PromptStatus::Published || $prompt->status === PromptStatus::Archived) {
                $prompt->forceFill(['reviewed_by' => $admin->id, 'reviewed_at' => now()])->save();
            }
        });

        // --- Avis, sur une partie des prompts publies -------------------------

        $publishedPrompts = $prompts->filter(fn (Prompt $prompt) => $prompt->status === PromptStatus::Published);

        foreach ($publishedPrompts->random(min(120, $publishedPrompts->count()))->all() as $prompt) {
            $reviewers = $buyers->random(random_int(1, 5));

            foreach ($reviewers as $reviewer) {
                if ($reviewer->id === $prompt->user_id) {
                    continue;
                }

                Review::query()->updateOrCreate(
                    ['prompt_id' => $prompt->id, 'user_id' => $reviewer->id],
                    ['rating' => random_int(3, 5), 'comment' => fake()->optional(0.4)->sentence()],
                );
            }
        }

        // --- Quelques packs, a partir des prompts publies de chaque createur ---

        foreach ($creators->random(8) as $creator) {
            $ownPublished = $publishedPrompts->where('user_id', $creator->id)->values();

            if ($ownPublished->count() < 2) {
                continue;
            }

            $selection = $ownPublished->random(min(3, $ownPublished->count()));
            $title = 'Kit '.fake()->words(2, true).' par '.$creator->name;

            $pack = Pack::create([
                'user_id' => $creator->id,
                'title' => $title,
                'slug' => Str::slug($title).'-'.random_int(1000, 999999),
                'description' => 'Une selection de prompts complementaires pour '.fake()->words(3, true).'.',
                'price' => round((float) $selection->sum('price') * 0.7, 2) ?: 9.99,
                'status' => PackStatus::Published,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            $pack->prompts()->attach($selection->pluck('id')->all());
        }

        $this->call(SubscriptionSeeder::class);
    }
}
