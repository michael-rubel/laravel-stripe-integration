<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\DataTransferObjects;

use Illuminate\Database\Eloquent\Model;
use MichaelRubel\StripeIntegration\Decorators\StripePaymentAmount;

final class OffsessionChargeData
{
    public function __construct(
        public readonly Model $model,
        public readonly StripePaymentAmount $payment_amount,
        public readonly array $intent_params = [],
        public readonly array $intent_options = [],
        public readonly array $confirmation_params = [],
        public readonly array $confirmation_options = [],
    ) {
    }
}
