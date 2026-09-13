<?php

namespace App\Domains\Payments\DTOs;

use App\Domains\Payments\Enums\GatewayStatus;

final readonly class PaymentResult
{
    public function __construct(
        public string $gateway,
        public string $externalReference,
        public GatewayStatus $status,
        public ?string $failureReason = null,
    ) {}
}
