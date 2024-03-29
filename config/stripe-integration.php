<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Stripe Payment Provider Credentials
    |--------------------------------------------------------------------------
    |
    | Credentials needed to work with the Stripe API.
    |
    */

    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),

];
