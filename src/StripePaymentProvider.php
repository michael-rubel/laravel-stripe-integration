<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration;

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
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

class StripePaymentProvider
{
    use PreparesPaymentMethods,
        ManagesPaymentIntents,
        ConfiguresCashier;

    /**
     * @extendability
     */
    use Macroable, Conditionable;

    /**
     * @param  StripeClient  $stripeClient
     *
     * @return void
     */
    public function __construct(public StripeClient $stripeClient)
    {
        //
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
            $data->payment_amount->getAmount(),
            $data->payment_method->id,
            $data->options,
        );
    }

    /**
     * Perform off-session charge.
     *
     * @param  OffsessionChargeData  $data
     *
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    public function offsessionCharge(OffsessionChargeData $data): PaymentIntent
    {
        $paymentIntent = $this->createPaymentIntent(new PaymentIntentData(
            paymentAmount: $data->payment_amount,
            model: $data->model,
            params: $data->intent_params,
            options: $data->intent_options,
        ));

        $confirmationParams = collect(['payment_method' => call($data->model)->defaultPaymentMethod()->id])
            ->merge($data->confirmation_params)
            ->toArray();

        return $this->confirmPaymentIntent(new PaymentIntentData(
            paymentIntent: $paymentIntent,
            params: $confirmationParams,
            options: $data->confirmation_options,
        ));
    }
}