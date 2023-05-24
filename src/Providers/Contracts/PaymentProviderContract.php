<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\Providers\Contracts;

interface PaymentProviderContract
{
    /**
     * @const
     */
    public const STRIPE_PAYMENT_PROVIDER = 'stripe';
}
