<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\Behaviors;

use Money\Currency;

trait ConfiguresCashier
{
    /**
     * Update the Cashier currency.
     *
     * @param  Currency  $currency
     *
     * @return void
     */
    public function cashierCurrencyAs(Currency $currency): void
    {
        config([
            'cashier.currency' => $currency->getCode(),
        ]);
    }
}
