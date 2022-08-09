<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\DataTransferObjects;

use Laravel\Cashier\PaymentMethod as CashierPaymentMethod;
use Spatie\DataTransferObject\DataTransferObject;
use Stripe\Customer;
use Stripe\PaymentMethod;

class PaymentMethodAttachmentData extends DataTransferObject
{
    /**
     * @var PaymentMethod|CashierPaymentMethod
     */
    public PaymentMethod|CashierPaymentMethod $paymentMethod;

    /**
     * @var Customer
     */
    public Customer $customer;

    /**
     * @var array
     */
    public array $params = [];

    /**
     * @var array
     */
    public array $options = [];
}
