<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\DataTransferObjects;

use Illuminate\Database\Eloquent\Model;
use MichaelRubel\StripeIntegration\Decorators\StripePaymentAmount;
use Spatie\DataTransferObject\DataTransferObject;

class PaymentIntentData extends DataTransferObject
{
    /**
     * @var Model
     */
    public Model $model;

    /**
     * @var StripePaymentAmount|null
     */
    public ?StripePaymentAmount $paymentAmount;

    /**
     * @var string|null
     */
    public ?string $intentId;

    /**
     * @var array
     */
    public array $params = [];

    /**
     * @var array
     */
    public array $options = [];
}
