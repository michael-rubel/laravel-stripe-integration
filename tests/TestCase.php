<?php

namespace MichaelRubel\StripeIntegration\Tests;

use Illuminate\Support\Collection;
use MichaelRubel\StripeIntegration\StripeIntegrationServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            StripeIntegrationServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('testing');
    }

    public function basicCharge(array $params): Collection
    {
        if (isset($params['data'])) {
            return collect([
                'amount'                      => $params['data']->payment_amount->getAmount(),
                'currency'                    => $params['data']->payment_amount->getCurrency()->getCode(),
                'description'                 => $params['data']?->options['description'],
                'payment_method'              => $params['data']->payment_method,
                'status'                      => 'succeeded',
            ]);
        }

        return new Collection;
    }

    /**
     * @param array $params
     *
     * @return Collection
     */
    public function offsessionCharge(array $params): Collection
    {
        return isset($params['data'])
            ? collect([
                'amount'          => $params['data']->payment_amount->getAmount(),
                'currency'        => $params['data']->payment_amount->getCurrency()->getCode(),
                'description'     => $params['data']?->intent_params['description'],
                'status'          => 'succeeded',
            ])
            : new Collection;
    }
}
