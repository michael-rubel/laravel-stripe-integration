<?php

namespace MichaelRubel\StripeIntegration\Tests;

use Illuminate\Support\Fluent;
use Laravel\Cashier\Payment;
use MichaelRubel\EnhancedContainer\Core\CallProxy;
use MichaelRubel\StripeIntegration\DataTransferObjects\OffsessionChargeData;
use MichaelRubel\StripeIntegration\DataTransferObjects\StripeChargeData;
use MichaelRubel\StripeIntegration\Decorators\Contracts\PaymentAmount;
use MichaelRubel\StripeIntegration\Decorators\StripePaymentAmountDecorator;
use MichaelRubel\StripeIntegration\Providers\Contracts\PaymentProviderContract;
use MichaelRubel\StripeIntegration\Providers\StripePaymentProvider;
use MichaelRubel\StripeIntegration\Tests\Stubs\User;
use Money\Currency;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;

class StripeChargeTest extends TestCase
{
    /**
     * @var User
     */
    private User $user;

    /**
     * @var CallProxy
     */
    private CallProxy $paymentProvider;

    /** @setUp */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = new User;

        $this->be($this->user);

        config(['stripe-integration.secret' => 'sk_test_test']);

        bind(PaymentAmount::class)->to(StripePaymentAmountDecorator::class);
        bind(PaymentProviderContract::class)->singleton(StripePaymentProvider::class);

        bind(StripePaymentProvider::class)->method('prepareCustomer', fn () => $this->user);
        bind(StripePaymentProvider::class)->method('updatePaymentMethod', fn () => new PaymentMethod);
        bind(StripePaymentProvider::class)->method('attachPaymentMethodToCustomer', fn () => true);
        bind(StripePaymentProvider::class)->method('setupIntentUsing', fn () => new Fluent());

        bind(StripePaymentProvider::class)->method()->charge(
            fn ($service, $app, $params) => new Payment(
                tap(new PaymentIntent('test_id'), function ($intent) use ($params) {
                    $this->basicCharge($params)->each(fn ($value, $key) => $intent->offsetSet($key, $value));
                })
            )
        );

        bind(StripePaymentProvider::class)->method()->offsessionCharge(
            fn ($service, $app, $params) => new Payment(
                tap(new PaymentIntent('test_id'), function ($intent) use ($params) {
                    $this->offsessionCharge($params)->each(fn ($value, $key) => $intent->offsetSet($key, $value));
                })
            )
        );
    }

    /** @test */
    public function basicUsageChargeTest()
    {
        $cost = app(PaymentAmount::class, [
            'currency' => new Currency('USD'),
            'amount'   => 1000,
        ]);

        $this->paymentProvider = call(PaymentProviderContract::class);

        $customer = $this->paymentProvider->prepareCustomer($this->user);

        $paymentMethod = $this->paymentProvider->updatePaymentMethod(
            $this->user,
            'test_payment_method'
        );

        $this->paymentProvider->attachPaymentMethodToCustomer(
            $paymentMethod,
            $customer
        );

        $chargeData = new StripeChargeData(
            model: $this->user,
            payment_amount: $cost,
            payment_method: $paymentMethod,
            options: ['description' => 'Test Stripe Charge Description'],
        );

        $payment = $this->paymentProvider->charge($chargeData);

        $this->assertStringContainsString('succeeded', $payment->status);
        $this->assertStringContainsString('Test Stripe Charge Description', $payment->description);
        $this->assertEquals($cost->getAmount(), $payment->amount);
    }

    /** @test */
    public function offsessionChargeTest()
    {
        $cost = app(PaymentAmount::class, [
            'currency' => new Currency('PLN'),
            'amount'   => 2000,
        ]);

        $this->paymentProvider = call(PaymentProviderContract::class);

        $customer = $this->paymentProvider->prepareCustomer($this->user);

        $paymentMethod = $this->paymentProvider->updatePaymentMethod(
            $this->user,
            'test_payment_method'
        );

        $this->paymentProvider->attachPaymentMethodToCustomer(
            $paymentMethod,
            $customer
        );

        $chargeData = new OffsessionChargeData(
            model: $this->user,
            payment_amount: $cost,
            intent_params: ['description' => 'Offsession Charge Description'],
        );

        $payment = $this->paymentProvider->offsessionCharge($chargeData);

        $this->assertStringContainsString('succeeded', $payment->status);
        $this->assertStringContainsString('Offsession Charge Description', $payment->description);
        $this->assertEquals($cost->getAmount(), $payment->amount);
    }
}
