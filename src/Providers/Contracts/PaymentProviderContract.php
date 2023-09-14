<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\Providers\Contracts;

use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Laravel\Cashier\Payment;
use Laravel\Cashier\PaymentMethod as CashierPaymentMethod;
use MichaelRubel\StripeIntegration\DataTransferObjects\OffsessionChargeData;
use MichaelRubel\StripeIntegration\DataTransferObjects\PaymentIntentData;
use MichaelRubel\StripeIntegration\DataTransferObjects\PaymentMethodAttachmentData;
use MichaelRubel\StripeIntegration\DataTransferObjects\StripeChargeData;
use Money\Currency;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\SetupIntent;

interface PaymentProviderContract
{
    /**
     * Set the cashier currency.
     *
     * @param  Currency  $currency
     *
     * @return void
     */
    public function setCashierCurrencyAs(Currency $currency): void;

    /**
     * Create the Stripe Setup Intent.
     *
     * @param  Model  $model
     * @param  array  $options
     *
     * @return SetupIntent
     */
    public function setupIntentUsing(Model $model, array $options = []): SetupIntent;

    /**
     * Create a payment intent.
     *
     * @param  PaymentIntentData  $data
     *
     * @return PaymentIntent
     */
    public function createPaymentIntent(PaymentIntentData $data): PaymentIntent;

    /**
     * Update the payment intent.
     *
     * @param  PaymentIntentData  $data
     *
     * @return PaymentIntent
     */
    public function updatePaymentIntent(PaymentIntentData $data): PaymentIntent;

    /**
     * Retrieve the payment intent.
     *
     * @param  PaymentIntentData  $data
     *
     * @return PaymentIntent
     */
    public function retrievePaymentIntent(PaymentIntentData $data): PaymentIntent;

    /**
     * Confirm the payment intent.
     *
     * @param  PaymentIntentData  $data
     *
     * @return PaymentIntent
     */
    public function confirmPaymentIntent(PaymentIntentData $data): PaymentIntent;

    /**
     * Prepare the customer.
     *
     * @param  Model  $model
     *
     * @return Customer
     */
    public function makeCustomerUsing(Model $model): Customer;

    /**
     * Update a default payment method.
     *
     * @param  Model  $model
     * @param  PaymentMethod|string  $paymentMethod
     *
     * @return CashierPaymentMethod|null
     */
    public function setPaymentMethodFor(Model $model, PaymentMethod|string $paymentMethod): ?CashierPaymentMethod;

    /**
     * Attach the payment method to the customer.
     *
     * @param  PaymentMethodAttachmentData  $data
     *
     * @return PaymentMethod
     */
    public function attachPaymentMethodToCustomer(PaymentMethodAttachmentData $data): PaymentMethod;

    /**
     * Perform a simple charge.
     *
     * @param  StripeChargeData  $data
     *
     * @return Payment
     * @throws IncompletePayment
     */
    public function charge(StripeChargeData $data): Payment;

    /**
     * Perform an "off-session" charge.
     *
     * @param  OffsessionChargeData  $data
     *
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    public function offsessionCharge(OffsessionChargeData $data): PaymentIntent;
}
