<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Macroable;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Laravel\Cashier\Payment;
use Laravel\Cashier\PaymentMethod as CashierPaymentMethod;
use MichaelRubel\StripeIntegration\DataTransferObjects\OffsessionChargeData;
use MichaelRubel\StripeIntegration\DataTransferObjects\PaymentIntentData;
use MichaelRubel\StripeIntegration\DataTransferObjects\PaymentMethodAttachmentData;
use MichaelRubel\StripeIntegration\DataTransferObjects\StripeChargeData;
use MichaelRubel\StripeIntegration\Providers\Contracts\PaymentProviderContract;
use Money\Currency;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\SetupIntent;
use Stripe\StripeClient;

class StripePaymentProvider implements PaymentProviderContract
{
    use Macroable;

    /**
     * @return void
     */
    public function __construct(
        protected StripeClient $stripeClient
    ) {
    }

    /**
     * Set the Cashier's currency.
     *
     * @param  Currency  $currency
     *
     * @return void
     */
    public function setCashierCurrency(Currency $currency): void
    {
        config([
            'cashier.currency' => $currency->getCode(),
        ]);
    }

    /**
     * Create the Stripe setup intent.
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
     * Prepare the customer to work with the payment system.
     *
     * @param  Model  $model
     *
     * @return Customer
     */
    public function prepareCustomer(Model $model): Customer
    {
        return call($model)->createOrGetStripeCustomer();
    }

    /**
     * Update the default payment method for model.
     *
     * @param  Model  $model
     * @param  PaymentMethod|string  $paymentMethod
     *
     * @return CashierPaymentMethod
     */
    public function updatePaymentMethod(Model $model, PaymentMethod|string $paymentMethod): CashierPaymentMethod
    {
        return call($model)->updateDefaultPaymentMethod($paymentMethod);
    }

    /**
     * Attach the payment method to the customer.
     *
     * @param  PaymentMethodAttachmentData  $data
     *
     * @return PaymentMethod
     */
    public function attachPaymentMethodToCustomer(PaymentMethodAttachmentData $data): PaymentMethod
    {
        $params = collect(['customer' => $data->customer->id])
            ->merge($data->params)
            ->toArray();

        return call($this->stripeClient->paymentMethods)->attach(
            $data->paymentMethod->id, $params, $data->options
        );
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
            $data->options
        );
    }

    /**
     * Perform an offsession charge.
     *
     * @param  OffsessionChargeData  $data
     *
     * @return PaymentIntent
     * @throws ApiErrorException|UnknownProperties
     */
    public function offsessionCharge(OffsessionChargeData $data): PaymentIntent
    {
        $paymentIntent = $this->createPaymentIntent(new PaymentIntentData(
            paymentAmount: $data->payment_amount,
            model: $data->model,
        ));

        $confirmation_params = collect(['payment_method' => call($data->model)->defaultPaymentMethod()->id])
            ->merge($data->confirmation_params)
            ->toArray();

        return $this->confirmPaymentIntent(new PaymentIntentData(
            paymentIntent: $paymentIntent,
            params: $confirmation_params,
            options: $data->confirmation_options,
        ));
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
        $makeIntentParams = collect([
            'amount' => $data->paymentAmount?->getAmount(),
            'currency' => $data->paymentAmount?->getCurrency()->getCode(),
            'payment_method_types' => ['card'],
        ])
        ->when($data->model?->stripeId(), fn ($params) => $params->merge([
            'customer' => $data->model?->stripeId(),
        ]))
        ->merge($data->params)
        ->toArray();

        return call($this->stripeClient->paymentIntents)->create($makeIntentParams, $data->options);
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
            $data->intentId, $updateIntentParams, $data->options
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
            $data->intentId, $data->params, $data->options
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
            $data->paymentIntent?->id, $data->params, $data->options
        );
    }
}
