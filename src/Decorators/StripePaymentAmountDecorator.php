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
     * @param float  $rawAmount
     * @param string $rawCurrency
     * @param int    $multiplier
     *
     * @throws \Exception
     */
    public function __construct(
        public float $rawAmount,
        public string $rawCurrency,
        public int $multiplier = 100
    ) {
        if ($this->multiplier === 0) {
            throw new \DivisionByZeroError('0 is forbidden.');
        }

        // Configures the payment amount decorator.
        // Used mainly for converting the payment amount
        // to payment-system friendly units.
        $this->money = new Money(
            $this->toPaymentSystemUnits(),
            new Currency($this->rawCurrency)
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
    public function toPaymentSystemUnits(): int
    {
        return (int) (
            (string) ($this->rawAmount * $this->multiplier)
        );
    }

    /**
     * Revert from "smallest common currency units".
     *
     * @return float
     */
    public function fromPaymentSystemUnits(): float
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
