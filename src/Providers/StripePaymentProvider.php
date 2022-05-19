<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Laravel\Cashier\Payment;
use Laravel\Cashier\PaymentMethod as CashierPaymentMethod;
use MichaelRubel\StripeIntegration\DataTransferObjects\OffsessionChargeData;
use MichaelRubel\StripeIntegration\DataTransferObjects\StripeChargeData;
use MichaelRubel\StripeIntegration\Decorators\StripePaymentAmount;
use MichaelRubel\StripeIntegration\Providers\Contracts\PaymentProviderContract;
use Money\Currency;
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
     * @param Currency $currency
     *
     * @return void
     */
    public function configureCashierCurrency(Currency $currency): void
    {
        config([
            'cashier.currency' => $currency->getCode(),
        ]);
    }

    /**
     * Create the Stripe setup intent.
     *
     * @param Model $model
     * @param array $options
     *
     * @return SetupIntent
     */
    public function setupIntentUsing(Model $model, array $options = []): SetupIntent
    {
        $options = collect([
            'usage' => 'off_session',
        ])->merge($options)->toArray();

        return call($model)->createSetupIntent($options);
    }

    /**
     * Prepare the customer to work with the payment system.
     *
     * @param Model $model
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
     * @param Model                $model
     * @param PaymentMethod|string $paymentMethod
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
     * @param PaymentMethod|CashierPaymentMethod $paymentMethod
     * @param Customer                           $customer
     * @param array                              $params
     * @param array                              $options
     *
     * @return PaymentMethod
     */
    public function attachPaymentMethodToCustomer(
        PaymentMethod|CashierPaymentMethod $paymentMethod,
        Customer $customer,
        array $params = [],
        array $options = []
    ): PaymentMethod {
        $params = collect([
            'customer' => $customer->id,
        ])->merge($params)->toArray();

        return call($this->stripeClient->paymentMethods)->attach(
            $paymentMethod->id,
            $params,
            $options
        );
    }

    /**
     * Perform a simple charge.
     *
     * @param StripeChargeData $data
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
     * Create a payment intent.
     *
     * @param StripePaymentAmount $paymentAmount
     * @param Model               $model
     * @param array               $params
     * @param array               $options
     *
     * @return PaymentIntent
     */
    public function createPaymentIntent(
        StripePaymentAmount $paymentAmount,
        Model $model,
        array $params = [],
        array $options = []
    ): PaymentIntent {
        return call($this->stripeClient->paymentIntents)->create(
            collect([
                'amount'               => $paymentAmount->getAmount(),
                'currency'             => $paymentAmount->getCurrency()->getCode(),
                'payment_method_types' => ['card'],
            ])
            ->when($model->stripeId(), fn ($params) => $params->merge([
                'customer' => $model->stripeId(),
            ]))
            ->merge($params)
            ->toArray(),
            $options
        );
    }

    /**
     * Update the payment intent.
     *
     * @param string $intent_id
     * @param Model  $model
     * @param array  $params
     * @param array  $options
     *
     * @return PaymentIntent
     *
     */
    public function updatePaymentIntent(string $intent_id, Model $model, array $params = [], array $options = []): PaymentIntent
    {
        return call($this->stripeClient->paymentIntents)->update(
            $intent_id,
            collect($params)
                ->when($model->stripeId(), fn ($params) => $params->merge([
                    'customer' => $model->stripeId(),
                ]))
                ->toArray(),
            $options
        );
    }

    /**
     * Retrieve the payment intent.
     *
     * @param string $intent_id
     * @param array  $params
     * @param array  $options
     *
     * @return PaymentIntent
     *
     * @throws ApiErrorException
     */
    public function retrievePaymentIntent(string $intent_id, array $params = [], array $options = []): PaymentIntent
    {
        return call($this->stripeClient->paymentIntents)->retrieve(
            $intent_id,
            $params,
            $options
        );
    }

    /**
     * Confirm the payment intent.
     *
     * @param PaymentIntent $paymentIntent
     * @param array         $confirmation_params
     * @param array         $confirmation_options
     *
     * @return PaymentIntent
     */
    public function confirmPaymentIntent(
        PaymentIntent $paymentIntent,
        array $confirmation_params = [],
        array $confirmation_options = []
    ): PaymentIntent {
        return call($this->stripeClient->paymentIntents)->confirm(
            $paymentIntent->id,
            $confirmation_params,
            $confirmation_options
        );
    }

    /**
     * Perform an offsession charge.
     *
     * @param OffsessionChargeData $data
     *
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    public function offsessionCharge(OffsessionChargeData $data): PaymentIntent
    {
        $paymentIntent = $this->createPaymentIntent(
            $data->payment_amount,
            $data->model
        );

        $confirmation_params = collect([
            'payment_method' => call($data->model)->defaultPaymentMethod()->id,
        ])->merge($data->confirmation_params)->toArray();

        return $this->confirmPaymentIntent(
            $paymentIntent,
            $confirmation_params,
            $data->confirmation_options
        );
    }
}
