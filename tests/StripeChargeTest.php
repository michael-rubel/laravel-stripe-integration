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

        bind(PaymentAmount::class)->to(StripePaymentAmount::class);
        bind(PaymentProviderContract::class)->singleton(StripePaymentProvider::class);

        bind(User::class)->method()->charge(
            fn ($service, $app, $params) => new Payment(
                tap(new PaymentIntent('test_id'), function ($intent) use ($params) {
                    $this->basicCharge($params)->each(fn ($value, $key) => $intent->offsetSet($key, $value));
                })
            )
        );

        bind(User::class)
            ->method()
            ->defaultPaymentMethod(fn () => new PaymentMethod('test_id'));

        bind(PaymentIntentService::class)
            ->method()
            ->create(fn () => new PaymentIntent('test_id'));

        bind(PaymentIntentService::class)
            ->method()
            ->confirm(
                fn () => tap(new PaymentIntent('test_id'), function ($intent) {
                    $this->offsessionCharge()->each(fn ($value, $key) => $intent->offsetSet($key, $value));
                })
            );
    }

    /** @test */
    public function basicUsageChargeTest()
    {
        $cost = app(PaymentAmount::class, [
            PaymentAmount::AMOUNT => 1000,
            PaymentAmount::CURRENCY => new Currency('USD'),
        ]);

        $this->paymentProvider = call(PaymentProviderContract::class);

        $customer = $this->paymentProvider->prepareCustomer($this->user);

        $paymentMethod = $this->paymentProvider->updatePaymentMethod(
            $this->user,
            'test_payment_method'
        );

        $this->paymentProvider->attachPaymentMethodToCustomer(
            new PaymentMethodAttachmentData(
                paymentMethod: $paymentMethod,
                customer: $customer,
            )
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
            PaymentAmount::AMOUNT => 1000,
            PaymentAmount::CURRENCY => new Currency('USD'),
        ]);

        $this->paymentProvider = call(PaymentProviderContract::class);

        $customer = $this->paymentProvider->prepareCustomer($this->user);

        $paymentMethod = $this->paymentProvider->updatePaymentMethod(
            $this->user,
            'test_payment_method'
        );

        $this->paymentProvider->attachPaymentMethodToCustomer(
            new PaymentMethodAttachmentData(
                paymentMethod: $paymentMethod,
                customer: $customer,
            )
        );

        $chargeData = new OffsessionChargeData(
            model: $this->user,
            payment_amount: $cost,
            intent_params: ['description' => 'Offsession Charge Description'],
        );

        $payment = $this->paymentProvider->offsessionCharge($chargeData);

        $this->assertStringContainsString('succeeded', $payment->status);
    }
}
