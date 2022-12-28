<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\Behaviors;

use Illuminate\Database\Eloquent\Model;
use MichaelRubel\StripeIntegration\DataTransferObjects\PaymentIntentData;
use Stripe\PaymentIntent;
use Stripe\SetupIntent;

trait ManagesPaymentIntents
{
    /**
     * Create the Stripe Setup Intent.
     *
     * @param  Model  $model
     * @param  array  $options
     *
     * @return SetupIntent
     */
    public function setupIntentUsing(Model $model, array $options = []): SetupIntent
    {
        $options = collect(['usage' => 'off_session'])
            ->merge($options)
            ->toArray();

        return call($model)->createSetupIntent($options);
    }

    /**
     * Create a payment intent.
     *
     * @param  PaymentIntentData  $data
     *
     * @return PaymentIntent
     */
    public function createPaymentIntent(PaymentIntentData $data): PaymentIntent
    {
        $intentParams = collect([
            'amount'               => $data->paymentAmount?->getAmount(),
            'currency'             => $data->paymentAmount?->getCurrency()->getCode(),
            'payment_method_types' => ['card'],
        ])
        ->when($data->model?->stripeId(), fn ($params) => $params->merge([
            'customer' => $data->model?->stripeId(),
        ]))
        ->merge($data->params)
        ->toArray();

        return call($this->stripeClient->paymentIntents)->create($intentParams, $data->options);
    }

    /**
     * Update the payment intent.
     *
     * @param  PaymentIntentData  $data
     *
     * @return PaymentIntent
     */
    public function updatePaymentIntent(PaymentIntentData $data): PaymentIntent
    {
        $updateIntentParams = collect($data->params)
            ->when($data->model?->stripeId(), fn ($params) => $params->merge([
                'customer' => $data->model?->stripeId(),
            ]))
            ->toArray();

        return call($this->stripeClient->paymentIntents)->update(
            $data->intentId, $updateIntentParams, $data->options,
        );
    }

    /**
     * Retrieve the payment intent.
     *
     * @param  PaymentIntentData  $data
     *
     * @return PaymentIntent
     */
    public function retrievePaymentIntent(PaymentIntentData $data): PaymentIntent
    {
        return call($this->stripeClient->paymentIntents)->retrieve(
            $data->intentId, $data->params, $data->options,
        );
    }

    /**
     * Confirm the payment intent.
     *
     * @param  PaymentIntentData  $data
     *
     * @return PaymentIntent
     */
    public function confirmPaymentIntent(PaymentIntentData $data): PaymentIntent
    {
        return call($this->stripeClient->paymentIntents)->confirm(
            $data->paymentIntent?->id ?? $data->intentId, $data->params, $data->options,
        );
    }
}
