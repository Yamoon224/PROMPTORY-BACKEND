<?php

namespace App\Domains\Sales\Http\Controllers;

use App\Domains\Sales\Contracts\SaleRepositoryContract;
use App\Domains\Sales\Http\Resources\SaleResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/** Historique des ventes : mes achats, mes ventes en tant que createur, ou tout (back-office). */
class SaleController extends Controller
{
    public function __construct(private readonly SaleRepositoryContract $sales) {}

    public function purchases(Request $request): AnonymousResourceCollection
    {
        return SaleResource::collection($this->sales->paginateForBuyer(
            $request->user()->id,
            $request->only(['sort', 'direction']),
            $request->integer('per_page', 15),
        ));
    }

    public function earnings(Request $request): AnonymousResourceCollection
    {
        return SaleResource::collection($this->sales->paginateForCreator(
            $request->user()->id,
            $request->only(['sort', 'direction']),
            $request->integer('per_page', 15),
        ));
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return SaleResource::collection($this->sales->paginate(
            $request->only(['payment_status', 'sort', 'direction']),
            $request->integer('per_page', 15),
        ));
    }
}
