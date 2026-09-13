<?php

namespace App\Models;

use App\Domains\Sales\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Vente d'un prompt ou d'un pack.
 *
 * @property int $id
 * @property string $client_reference
 * @property int|null $prompt_id
 * @property int|null $pack_id
 * @property int $buyer_id
 * @property int $creator_id
 * @property float $price
 * @property float $commission_rate
 * @property float $commission
 * @property PaymentStatus $payment_status
 */
class Sale extends Model
{
    protected $fillable = [
        'client_reference', 'prompt_id', 'pack_id', 'buyer_id', 'creator_id',
        'price', 'commission_rate', 'commission', 'payment_status', 'payment_gateway', 'payment_reference',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'commission' => 'decimal:2',
            'payment_status' => PaymentStatus::class,
        ];
    }

    /** @return BelongsTo<Prompt, $this> */
    public function prompt(): BelongsTo
    {
        return $this->belongsTo(Prompt::class);
    }

    /** @return BelongsTo<Pack, $this> */
    public function pack(): BelongsTo
    {
        return $this->belongsTo(Pack::class);
    }

    /** @return BelongsTo<User, $this> */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
