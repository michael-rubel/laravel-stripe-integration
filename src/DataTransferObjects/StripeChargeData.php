<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\DataTransferObjects;

use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\PaymentMethod as CashierPaymentMethod;
use MichaelRubel\StripeIntegration\Decorators\Contracts\PaymentAmount;
use Stripe\PaymentMethod;

final class StripeChargeData
{
    public function __construct(
        public readonly Model $model,
        public readonly PaymentAmount $paymentAmount,
        public readonly CashierPaymentMethod|PaymentMethod $paymentMethod,
        public readonly array $options = [],
    ) {
    }
}
