<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration\DataTransferObjects;

use Illuminate\Database\Eloquent\Model;
use MichaelRubel\StripeIntegration\Decorators\Contracts\PaymentAmount;
use Spatie\DataTransferObject\DataTransferObject;

class OffsessionChargeData extends DataTransferObject
{
    /**
     * @var Model
     */
    public Model $model;

    /**
     * @var PaymentAmount
     */
    public PaymentAmount $payment_amount;

    /**
     * @var array
     */
    public array $intent_params = [];

    /**
     * @var array
     */
    public array $intent_options = [];

    /**
     * @var array
     */
    public array $confirmation_params = [];

    /**
     * @var array
     */
    public array $confirmation_options = [];
}
