<?php

namespace Database\Factories;

use App\Domains\Prompts\Enums\PromptStatus;
use App\Models\Prompt;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Prompt>
 */
class PromptFactory extends Factory
{
    protected $model = Prompt::class;

    /** @var list<string> */
    private const TEMPLATES = [
        'Redacteur de fiches produit %s',
        'Generateur de posts LinkedIn sur %s',
        'Assistant de reponses aux avis clients %s',
        'Createur de plans de cours sur %s',
        'Redacteur d\'e-mails de prospection %s',
        'Generateur d\'idees de contenu %s',
        'Assistant de relecture et correction %s',
        'Createur de scripts video courts %s',
        'Redacteur de descriptions %s',
        'Generateur de FAQ %s',
        'Assistant de brainstorming %s',
        'Createur de sequences e-mailing %s',
        'Redacteur de communiques de presse %s',
        'Generateur de titres accrocheurs %s',
        'Assistant d\'analyse de donnees %s',
        'Createur de personas %s',
        'Redacteur de fiches de poste %s',
        'Generateur de quiz %s',
        'Assistant de negociation %s',
        'Createur de storyboards %s',
        'Debuggeur de code %s',
        'Generateur de tests unitaires %s',
        'Assistant de revue de code %s',
        'Redacteur de documentation technique %s',
        'Createur de prompts d\'image %s',
    ];

    /** @var list<string> */
    private const TOPICS = [
        'e-commerce', 'SaaS B2B', 'immobilier', 'coaching sportif', 'restauration',
        'voyage', 'mode', 'beaute', 'finance personnelle', 'education en ligne',
        'sante et bien-etre', 'artisanat', 'freelance', 'startups tech', 'associations',
        'agences marketing', 'developpement web', 'photographie', 'musique', 'jeux video',
    ];

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $title = trim(sprintf($this->faker->randomElement(self::TEMPLATES), $this->faker->randomElement(self::TOPICS)));

        $price = $this->faker->randomElement([
            0, 0, 0, 2.99, 3.49, 3.99, 4.49, 4.99, 5.99, 6.99, 7.99, 9.99, 12.99, 14.99, 19.99,
        ]);

        $status = $this->faker->randomElement([
            ...array_fill(0, 85, PromptStatus::Published),
            ...array_fill(0, 8, PromptStatus::PendingValidation),
            ...array_fill(0, 5, PromptStatus::Draft),
            ...array_fill(0, 2, PromptStatus::Archived),
        ]);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.$this->faker->unique()->numberBetween(1000, 9_999_999),
            'content' => $this->generateContent($title),
            'price' => $price,
            'status' => $status,
            'views_count' => $this->faker->numberBetween(0, 6000),
            'downloads_count' => $this->faker->numberBetween(0, 900),
        ];
    }

    private function generateContent(string $title): string
    {
        return sprintf(
            "Tu es un expert reconnu du sujet suivant : « %s ».\n\n".
            "Contexte : {contexte}\n".
            "Objectif : {objectif}\n".
            "Contraintes : {contraintes}\n\n".
            "Produis un resultat clair, structure et directement utilisable, sans commentaire superflu.",
            $title,
        );
    }
}
