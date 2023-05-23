<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\DataTransferObjects;

use Laravel\Cashier\PaymentMethod as CashierPaymentMethod;
use Stripe\Customer;
use Stripe\PaymentMethod;

final class PaymentMethodAttachmentData
{
    public function __construct(
        public PaymentMethod|CashierPaymentMethod $paymentMethod,
        public Customer $customer,
        public array $params = [],
        public array $options = [],
    ) {
    }
}
