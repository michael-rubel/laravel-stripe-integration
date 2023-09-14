<?php

namespace MichaelRubel\StripeIntegration\Tests;

use Laravel\Cashier\Payment;
use MichaelRubel\StripeIntegration\DataTransferObjects\StripeChargeData;
use MichaelRubel\StripeIntegration\Decorators\StripePaymentAmount;
use MichaelRubel\StripeIntegration\Providers\StripePaymentProvider;
use MichaelRubel\StripeIntegration\Tests\Stubs\User;
use Stripe\PaymentIntent;

class ConditionableTest extends TestCase
{
    /**
     * @var User
     */
    private User $user;

    /** @setUp */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = new User;

        $this->be($this->user);

        config(['stripe-integration.secret' => 'sk_test_test']);

        bind(User::class)->method()->charge(
            fn ($service, $app, $params) => new Payment(
                tap(new PaymentIntent('test_id'), function ($intent) use ($params) {
                    $this->basicCharge($params)->each(fn ($value, $key) => $intent->offsetSet($key, $value));
                })
            )
        );
    }

    /** @test */
    public function testCanUseWhenInPaymentProvider()
    {
        $payment = call(StripePaymentProvider::class)->when(true, function ($provider) {
            return $provider->charge(new StripeChargeData(
                model: $this->user,
                paymentAmount: new StripePaymentAmount(100, 'usd'),
                paymentMethod: $provider->setPaymentMethodFor($this->user, 'test_paymentMethod'),
                options: ['description' => 'test'],
            ));
        });

        $this->assertStringContainsString('succeeded', $payment->status);
        $this->assertEquals(10000, $payment->amount);
        $this->assertSame('test', $payment->description);
    }

    public function testCanUseUnlessInPaymentProvider()
    {
        $payment = call(StripePaymentProvider::class)->unless(false, function ($provider) {
            return $provider->charge(new StripeChargeData(
                model: $this->user,
                paymentAmount: new StripePaymentAmount(200, 'usd'),
                paymentMethod: $provider->setPaymentMethodFor($this->user, 'test_paymentMethod'),
                options: ['description' => 'test2'],
            ));
        });

        $this->assertStringContainsString('succeeded', $payment->status);
        $this->assertEquals(20000, $payment->amount);
        $this->assertSame('test2', $payment->description);
    }
}
