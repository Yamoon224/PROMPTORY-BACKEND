<?php

namespace App\Domains\Prompts\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Prompt
 *
 * Le contenu integral (`content`) n'est jamais renvoye pour un prompt payant
 * tant que le lecteur ne l'a pas achete : c'est ce qui empeche un achat de se
 * resumer a « ouvrir les outils de developpement ». `PromptController` appelle
 * `hideContent()` avant de renvoyer la ressource des qu'il ne peut pas prouver
 * que le lecteur a paye.
 */
class PromptResource extends JsonResource
{
    private bool $revealContent = true;

    public function hideContent(): static
    {
        $this->revealContent = false;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->revealContent ? $this->content : null,
            'price' => (float) $this->price,
            'is_free' => (float) $this->price === 0.0,
            'status' => $this->status->value,
            'rejection_reason' => $this->when($this->status->value === 'draft', $this->rejection_reason),
            'views_count' => $this->views_count,
            'downloads_count' => $this->downloads_count,
            // Champs toujours presents (jamais `when()`/`whenCounted()`) : le
            // contrat d'API les declare `nullable`, pas absents. Un champ tantot
            // present tantot manquant selon la requete d'origine (avec ou sans
            // `withCount`) est le genre d'incoherence qu'un frontend type
            // decouvre en production par un `undefined.toFixed is not a
            // function`, jamais en developpement.
            'reviews_count' => (int) ($this->reviews_count ?? 0),
            'average_rating' => $this->reviews_avg_rating !== null ? round((float) $this->reviews_avg_rating, 1) : null,
            'creator' => $this->whenLoaded('user', fn () => ['id' => $this->user->id, 'name' => $this->user->name]),
            'folder_id' => $this->folder_id,
            'tags' => $this->whenLoaded('tags', fn () => $this->tags->map(fn ($tag) => [
                'id' => $tag->id, 'name' => $tag->name, 'slug' => $tag->slug,
            ])),
            'categories' => $this->whenLoaded('categories', fn () => $this->categories->map(fn ($category) => [
                'id' => $category->id, 'name' => $category->name, 'slug' => $category->slug,
            ])),
            'ia_models' => $this->whenLoaded('iaModels', fn () => $this->iaModels->map(fn ($model) => [
                'id' => $model->id, 'name' => $model->name, 'slug' => $model->slug,
            ])),
            'attachments' => $this->whenLoaded('attachments', fn () => $this->attachments->map(fn ($attachment) => [
                'id' => $attachment->id, 'type' => $attachment->type->value, 'url' => $attachment->url,
            ])),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
