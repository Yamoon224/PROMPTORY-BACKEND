<?php

namespace App\Domains\Shared\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Sonde de sante consommee par l'orchestrateur et les tests de deploiement.
 *
 * La connectivite base est verifiee explicitement : une API qui repond 200
 * alors que sa base est injoignable est le pire des signaux pour un
 * deploiement automatise.
 */
class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $database = $this->databaseState();
        $healthy = $database === 'ok';

        return response()->json([
            'status' => $healthy ? 'ok' : 'degraded',
            'checks' => ['database' => $database],
            'payment_methods' => array_keys((array) config('promptory.gateways', [])),
            'timestamp' => now()->toIso8601String(),
        ], $healthy ? 200 : 503);
    }

    private function databaseState(): string
    {
        try {
            DB::connection()->getPdo();

            return 'ok';
        } catch (Throwable) {
            return 'unreachable';
        }
    }
}
