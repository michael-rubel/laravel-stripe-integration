<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\DataTransferObjects;

use Illuminate\Database\Eloquent\Model;
use MichaelRubel\StripeIntegration\Decorators\StripePaymentAmount;
use Stripe\PaymentIntent;

final class PaymentIntentData
{
    public function __construct(
        public readonly ?string $intentId = null,
        public readonly ?PaymentIntent $paymentIntent = null,
        public readonly ?StripePaymentAmount $paymentAmount = null,
        public readonly ?Model $model = null,
        public readonly array $params = [],
        public readonly array $options = [],
    ) {
    }
}
