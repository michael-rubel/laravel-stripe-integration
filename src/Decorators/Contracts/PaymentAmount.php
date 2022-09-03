<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\Decorators\Contracts;

use Money\Currency;

interface PaymentAmount
{
    /**
     * Const-strings of an input.
     *
     * @const
     */
    public const AMOUNT = 'amount';
    public const CURRENCY = 'currency';

    /**
     * Return the value.
     *
     * @return int
     */
    public function getAmount(): int;

    /**
     * Return the currency.
     *
     * @return Currency
     */
    public function getCurrency(): Currency;

    /**
     * Convert to "smallest common currency units".
     *
     * @return int
     */
    public function toPaymentSystemUnits(): int;

    /**
     * Revert from "smallest common currency units".
     *
     * @return float
     */
    public function fromPaymentSystemUnits(): float;
}
