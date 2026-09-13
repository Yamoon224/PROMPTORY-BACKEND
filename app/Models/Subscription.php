<?php

namespace App\Models;

use App\Domains\Subscriptions\Enums\SubscriptionStatus;
use App\Domains\Subscriptions\Enums\SubscriptionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property SubscriptionType $type
 * @property float $price
 * @property SubscriptionStatus $status
 */
class Subscription extends Model
{
    protected $fillable = [
        'user_id', 'type', 'price', 'status', 'start_date', 'end_date', 'payment_gateway', 'payment_reference',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'type' => SubscriptionType::class,
            'status' => SubscriptionStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Vrai si l'abonnement est actif a la date du jour, pas seulement par son statut enregistre. */
    public function isCurrentlyActive(): bool
    {
        return $this->status === SubscriptionStatus::Active
            && $this->end_date !== null
            && $this->end_date->isFuture();
    }
}
