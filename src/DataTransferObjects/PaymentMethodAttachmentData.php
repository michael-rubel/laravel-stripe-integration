<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\DataTransferObjects;

use Laravel\Cashier\PaymentMethod as CashierPaymentMethod;
use Stripe\Customer;
use Stripe\PaymentMethod;

final class PaymentMethodAttachmentData
{
    public function __construct(
        public readonly PaymentMethod|CashierPaymentMethod $paymentMethod,
        public readonly Customer $customer,
        public readonly array $params = [],
        public readonly array $options = [],
    ) {
    }
}
