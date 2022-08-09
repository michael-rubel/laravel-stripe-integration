<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\DataTransferObjects;

use Illuminate\Database\Eloquent\Model;
use MichaelRubel\StripeIntegration\Decorators\StripePaymentAmount;
use Spatie\DataTransferObject\DataTransferObject;

class PaymentIntentData extends DataTransferObject
{
    /**
     * @var StripePaymentAmount
     */
    public StripePaymentAmount $paymentAmount;

    /**
     * @var Model
     */
    public Model $model;

    /**
     * @var array
     */
    public array $params  = [];

    /**
     * @var array
     */
    public array $options = [];
}
