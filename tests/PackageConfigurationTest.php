<?php

namespace MichaelRubel\StripeIntegration\Tests;

use Stripe\StripeClient;

class PackageConfigurationTest extends TestCase
{
    public function testPackageBindsStripeClientWithCredentialsFromConfig()
    {
        $this->app['config']->set('stripe-integration.secret', 'test-key');

        $client = $this->app->make(StripeClient::class);

        $this->assertSame('test-key', $client->getApiKey());
    }
}
