<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\Decorators;

use MichaelRubel\StripeIntegration\Decorators\Contracts\PaymentAmount;
use Money\Currency;
use Money\Money;

class StripePaymentAmountDecorator implements PaymentAmount
{
    /**
     * @var Money
     */
    public Money $money;

    /**
     * @param float  $amount
     * @param string $currency
     * @param int    $multiplier
     *
     * @throws \Exception
     */
    public function __construct(
        public float $amount,
        public string $currency,
        public int $multiplier = 100
    ) {
        if ($this->multiplier === 0) {
            throw new \DivisionByZeroError('0 is forbidden.');
        }

        if (empty($this->currency)) {
            throw new \Exception('The currency is empty.');
        }

        // Configures the payment amount decorator.
        // Used mainly for converting the payment amount
        // to payment-system friendly units.
        $this->money = new Money(
            $this->convertToPaymentSystemUnits(),
            new Currency($this->currency)
        );
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return (int) $this->money->getAmount();
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->money->getCurrency();
    }

    /**
     * Convert to "smallest common currency units".
     *
     * @return int
     */
    public function convertToPaymentSystemUnits(): int
    {
        return (int) (
            (string) ($this->amount * $this->multiplier)
        );
    }

    /**
     * Revert from "smallest common currency units".
     *
     * @return float
     */
    public function revertFromPaymentSystemUnits(): float
    {
        return (float) (
            (int) $this->money->getAmount() / $this->multiplier
        );
    }

    /**
     * Forward call to the money object if the method doesn't exist in the decorator.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters): mixed
    {
        return call($this->money)->{$method}(...$parameters);
    }
}