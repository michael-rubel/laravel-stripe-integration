<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\DataTransferObjects;

use Illuminate\Database\Eloquent\Model;
use MichaelRubel\StripeIntegration\Decorators\StripePaymentAmount;

final class OffsessionChargeData
{
    public function __construct(
        public readonly Model $model,
        public readonly StripePaymentAmount $paymentAmount,
        public readonly array $intentParams = [],
        public readonly array $intentOptions = [],
        public readonly array $confirmationParams = [],
        public readonly array $confirmationOptions = [],
    ) {
    }
}
