<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\DataTransferObjects;

use Illuminate\Database\Eloquent\Model;
use MichaelRubel\StripeIntegration\Decorators\StripePaymentAmount;
use Spatie\DataTransferObject\DataTransferObject;
use Stripe\PaymentIntent;

class PaymentIntentData extends DataTransferObject
{
    /**
     * @var string|null
     */
    public ?string $intentId;

    /**
     * @var PaymentIntent|null
     */
    public ?PaymentIntent $paymentIntent;

    /**
     * @var StripePaymentAmount|null
     */
    public ?StripePaymentAmount $paymentAmount;

    /**
     * @var Model|null
     */
    public ?Model $model;

    /**
     * @var array
     */
    public array $params = [];

    /**
     * @var array
     */
    public array $options = [];
}
