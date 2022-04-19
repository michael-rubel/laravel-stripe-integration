<?php

namespace MichaelRubel\StripeIntegration\Tests\Stubs;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Laravel\Cashier\Billable;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable,
        Authorizable,
        Billable;
}
