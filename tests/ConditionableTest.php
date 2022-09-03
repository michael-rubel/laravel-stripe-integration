<?php

namespace MichaelRubel\StripeIntegration\Tests;

use Laravel\Cashier\Payment;
use MichaelRubel\EnhancedContainer\Core\CallProxy;
use MichaelRubel\StripeIntegration\DataTransferObjects\OffsessionChargeData;
use MichaelRubel\StripeIntegration\DataTransferObjects\PaymentMethodAttachmentData;
use MichaelRubel\StripeIntegration\DataTransferObjects\StripeChargeData;
use MichaelRubel\StripeIntegration\Decorators\Contracts\PaymentAmount;
use MichaelRubel\StripeIntegration\Decorators\StripePaymentAmount;
use MichaelRubel\StripeIntegration\Providers\Contracts\PaymentProviderContract;
use MichaelRubel\StripeIntegration\Providers\StripePaymentProvider;
use MichaelRubel\StripeIntegration\Tests\Stubs\User;
use Money\Currency;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Service\PaymentIntentService;

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
                payment_amount: new StripePaymentAmount(100, 'usd'),
                payment_method: $provider->setPaymentMethodFor($this->user, 'test_payment_method'),
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
                payment_amount: new StripePaymentAmount(200, 'usd'),
                payment_method: $provider->setPaymentMethodFor($this->user, 'test_payment_method'),
                options: ['description' => 'test2'],
            ));
        });

        $this->assertStringContainsString('succeeded', $payment->status);
        $this->assertEquals(20000, $payment->amount);
        $this->assertSame('test2', $payment->description);
    }
}
