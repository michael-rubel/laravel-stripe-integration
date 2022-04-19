<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Macroable;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Laravel\Cashier\Payment;
use Laravel\Cashier\PaymentMethod as CashierPaymentMethod;
use MichaelRubel\StripeIntegration\DataTransferObjects\OffsessionChargeData;
use MichaelRubel\StripeIntegration\DataTransferObjects\StripeChargeData;
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
     * @var StripeClient
     */
    protected StripeClient $stripeClient;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->stripeClient = new StripeClient(
            config('stripe-integration.secret')
        );
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

        return $model->createSetupIntent($options);
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
        return $model->createOrGetStripeCustomer();
    }

    /**
     * Updates the default payment method for model.
     *
     * @param Model                $model
     * @param PaymentMethod|string $paymentMethod
     *
     * @return CashierPaymentMethod
     */
    public function updatePaymentMethod(Model $model, PaymentMethod|string $paymentMethod): CashierPaymentMethod
    {
        return $model->updateDefaultPaymentMethod($paymentMethod);
    }

    /**
     * Attaches the payment method to the customer.
     *
     * @param PaymentMethod|CashierPaymentMethod $paymentMethod
     * @param Customer                           $customer
     * @param array                              $params
     * @param array                              $options
     *
     * @throws ApiErrorException
     */
    public function attachPaymentMethodToCustomer(
        PaymentMethod|CashierPaymentMethod $paymentMethod,
        Customer $customer,
        array $params = [],
        array $options = []
    ): void {
        $params = collect([
            'customer' => $customer->id,
        ])->merge($params)->toArray();

        $this->stripeClient->paymentMethods->attach(
            $paymentMethod->id,
            $params,
            $options
        );
    }

    /**
     * Simple charge.
     *
     * @param StripeChargeData $data
     *
     * @return Payment|null
     * @throws IncompletePayment
     */
    public function charge(StripeChargeData $data): ?Payment
    {
        return $data->model->charge(
            $data->payment_amount->getAmount(),
            $data->payment_method->id,
            $data->options
        );
    }

    /**
     * Offsession charge.
     *
     * @param OffsessionChargeData $data
     *
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    public function offsessionCharge(OffsessionChargeData $data): PaymentIntent
    {
        $intent_params = collect([
            'amount'               => $data->payment_amount->convertToPaymentSystemUnits(),
            'currency'             => $data->payment_amount->getCurrency()->getCode(),
            'customer'             => $data->model->stripe_id,
            'payment_method_types' => ['card'],
        ])->merge($data->intent_params)->toArray();

        $paymentIntent = $this->stripeClient->paymentIntents->create(
            $intent_params,
            $data->intent_options
        );

        $confirmation_params = collect([
            'payment_method' => $data->model->defaultPaymentMethod()->id,
        ])->merge($data->confirmation_params)->toArray();

        return $this->stripeClient->paymentIntents->confirm(
            $paymentIntent->id,
            $confirmation_params,
            $data->confirmation_options
        );
    }
}
