<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\Behaviors;

use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\PaymentMethod as CashierPaymentMethod;
use MichaelRubel\StripeIntegration\DataTransferObjects\PaymentMethodAttachmentData;
use Stripe\Customer;
use Stripe\PaymentMethod;

trait PreparesPaymentMethods
{
    /**
     * Prepare the customer.
     *
     * @param  Model  $model
     *
     * @return Customer
     */
    public function makeCustomerUsing(Model $model): Customer
    {
        return call($model)->createOrGetStripeCustomer();
    }

    /**
     * Update the default payment method.
     *
     * @param  Model  $model
     * @param  PaymentMethod|string  $paymentMethod
     *
     * @return CashierPaymentMethod
     */
    public function setPaymentMethodFor(Model $model, PaymentMethod|string $paymentMethod): CashierPaymentMethod
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
}
