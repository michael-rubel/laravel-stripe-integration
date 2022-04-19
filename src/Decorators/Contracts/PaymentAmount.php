<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\Decorators\Contracts;

use Money\Currency;

interface PaymentAmount
{
    /**
     * Returns the value represented by this object.
     *
     * @return int
     */
    public function getAmount(): int;

    /**
     * Returns the currency of this object.
     *
     * @return Currency
     */
    public function getCurrency(): Currency;

    /**
     * Convert to "smallest common currency units".
     *
     * @return int
     */
    public function convertToPaymentSystemUnits(): int;

    /**
     * Revert from "smallest common currency units".
     *
     * @return float
     */
    public function revertFromPaymentSystemUnits(): float;
}
