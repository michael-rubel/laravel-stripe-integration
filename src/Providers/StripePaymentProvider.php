<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\Providers;

use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Laravel\Cashier\Payment;
use MichaelRubel\StripeIntegration\Behaviors\ConfiguresCashier;
use MichaelRubel\StripeIntegration\Behaviors\ManagesPaymentIntents;
use MichaelRubel\StripeIntegration\Behaviors\PreparesPaymentMethods;
use MichaelRubel\StripeIntegration\DataTransferObjects\OffsessionChargeData;
use MichaelRubel\StripeIntegration\DataTransferObjects\PaymentIntentData;
use MichaelRubel\StripeIntegration\DataTransferObjects\StripeChargeData;
use MichaelRubel\StripeIntegration\Providers\Contracts\PaymentProviderContract;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

class StripePaymentProvider implements PaymentProviderContract
{
    /** @extendability */
    use Conditionable, Macroable;

    /** @behaviors */
    use ConfiguresCashier,
        ManagesPaymentIntents,
        PreparesPaymentMethods;

    /**
     * @param  StripeClient  $stripeClient
     *
     * @return void
     */
    public function __construct(
        public StripeClient $stripeClient
    ) {
    }

    /**
     * Perform a simple charge.
     *
     * @param  StripeChargeData  $data
     *
     * @return Payment
     * @throws IncompletePayment
     */
    public function charge(StripeChargeData $data): Payment
    {
        return call($data->model)->charge(
            $data->paymentAmount->getAmount(),
            $data->paymentMethod->id,
            $data->options,
        );
    }

    /**
     * Perform an "off-session" charge.
     *
     * @param  OffsessionChargeData  $data
     *
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    public function offsessionCharge(OffsessionChargeData $data): PaymentIntent
    {
        $paymentIntent = $this->createPaymentIntent(new PaymentIntentData(
            paymentAmount: $data->paymentAmount,
            model: $data->model,
            params: $data->intentParams,
            options: $data->intentOptions,
        ));

        $confirmationParams = collect(['paymentMethod' => call($data->model)->defaultPaymentMethod()?->id])
            ->merge($data->confirmationParams)
            ->toArray();

        return $this->confirmPaymentIntent(new PaymentIntentData(
            paymentIntent: $paymentIntent,
            params: $confirmationParams,
            options: $data->confirmationOptions,
        ));
    }
}
