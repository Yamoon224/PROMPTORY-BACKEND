<?php

namespace App\Domains\Prompts\Http\Controllers;

use App\Domains\Prompts\Http\Requests\StorePromptRequest;
use App\Domains\Prompts\Http\Requests\UpdatePromptRequest;
use App\Domains\Prompts\Http\Resources\PromptResource;
use App\Domains\Prompts\Services\PromptService;
use App\Domains\Sales\Services\PurchaseAccessService;
use App\Domains\Shared\Exceptions\OwnershipViolationException;
use App\Http\Controllers\Controller;
use App\Models\Prompt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PromptController extends Controller
{
    public function __construct(
        private readonly PromptService $prompts,
        private readonly PurchaseAccessService $access,
    ) {}

    /** Vitrine publique : uniquement les prompts publies, sans jamais exposer leur contenu. */
    public function index(Request $request): AnonymousResourceCollection
    {
        $prompts = $this->prompts->browsePublished(
            $request->only(['search', 'category', 'tag', 'ia_model', 'free', 'min_price', 'max_price', 'sort', 'direction']),
            $request->integer('per_page', 15),
        );

        // `AnonymousResourceCollection` n'a pas de veritable `each()` : l'appel
        // serait devie par `JsonResource::__call` vers le paginateur enveloppe,
        // dont le `each()` renvoie une simple Collection — pas la ressource. On
        // parcourt donc directement la collection de ressources deja construite,
        // puis on renvoie cette meme instance.
        $resources = PromptResource::collection($prompts);
        $resources->collection->each(fn (PromptResource $resource) => $resource->hideContent());

        return $resources;
    }

    /** Mes prompts (dashboard createur), tous statuts confondus. */
    public function mine(Request $request): AnonymousResourceCollection
    {
        return PromptResource::collection($this->prompts->listMine(
            $request->user()->id,
            $request->only(['status', 'folder_id', 'sort', 'direction']),
            $request->integer('per_page', 15),
        ));
    }

    public function store(StorePromptRequest $request): JsonResponse
    {
        $prompt = $this->prompts->create($request->user()->id, $request->validated());

        return (new PromptResource($prompt))->response()->setStatusCode(201);
    }

    /** Fiche publique par slug : contenu masque tant que le lecteur n'a pas acces. */
    public function show(Request $request, string $slug): PromptResource
    {
        $prompt = $this->prompts->findBySlugForViewing($slug, $request->user()?->id);
        $resource = new PromptResource($prompt->load(['reviews.user:id,name']));

        if (! $this->access->canRead($prompt, $request->user())) {
            $resource->hideContent();
        }

        return $resource;
    }

    public function update(UpdatePromptRequest $request, Prompt $prompt): PromptResource
    {
        $this->authorizeOwner($request, $prompt);

        return new PromptResource($this->prompts->update($prompt, $request->validated()));
    }

    public function destroy(Request $request, Prompt $prompt): Response
    {
        $this->authorizeOwner($request, $prompt);
        $this->prompts->delete($prompt);

        return response()->noContent();
    }

    private function authorizeOwner(Request $request, Prompt $prompt): void
    {
        if ($prompt->user_id !== $request->user()->id) {
            throw OwnershipViolationException::make();
        }
    }
}
