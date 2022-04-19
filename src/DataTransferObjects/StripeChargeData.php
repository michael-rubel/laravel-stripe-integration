<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\DataTransferObjects;

use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\PaymentMethod as CashierPaymentMethod;
use MichaelRubel\StripeIntegration\Decorators\Contracts\PaymentAmount;
use Spatie\DataTransferObject\DataTransferObject;
use Stripe\PaymentMethod;

class StripeChargeData extends DataTransferObject
{
    /**
     * @var Model
     */
    public Model $model;

    /**
     * @var PaymentAmount
     */
    public PaymentAmount $paymentAmount;

    /**
     * @var CashierPaymentMethod|PaymentMethod
     */
    public CashierPaymentMethod|PaymentMethod $paymentMethod;

    /**
     * @var array
     */
    public array $options = [];
}
