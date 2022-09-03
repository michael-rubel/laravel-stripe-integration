<?php

namespace MichaelRubel\StripeIntegration\Tests;

use Laravel\Cashier\PaymentMethod as CashierPaymentMethod;
use MichaelRubel\EnhancedContainer\Call;
use MichaelRubel\StripeIntegration\DataTransferObjects\PaymentIntentData;
use MichaelRubel\StripeIntegration\DataTransferObjects\PaymentMethodAttachmentData;
use MichaelRubel\StripeIntegration\Decorators\StripePaymentAmount;
use MichaelRubel\StripeIntegration\Providers\StripePaymentProvider;
use MichaelRubel\StripeIntegration\Tests\Stubs\User;
use Money\Currency;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Service\PaymentIntentService;
use Stripe\SetupIntent;
use Stripe\StripeClient;

class StripePaymentProviderTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        bind(PaymentIntentService::class)
            ->method()
            ->create(fn () => new PaymentIntent('test_id'));

        bind(PaymentIntentService::class)
            ->method()
            ->confirm(fn () => new PaymentIntent('test_id'));

        bind(PaymentIntentService::class)
            ->method()
            ->update(fn () => new PaymentIntent('test_id'));

        bind(PaymentIntentService::class)
            ->method()
            ->retrieve(fn () => new PaymentIntent('test_id'));
    }

    /** @test */
    public function testCanInstantiateStripePaymentProvider()
    {
        $paymentProvider = new StripePaymentProvider(
            new StripeClient(
                config('stripe-integration.secret')
            )
        );

        $this->assertInstanceOf(StripePaymentProvider::class, $paymentProvider);
    }

    /** @test */
    public function testCanInstantiateStripePaymentProviderThroughContainer()
    {
        $paymentProvider = app(StripePaymentProvider::class);

        $this->assertInstanceOf(StripePaymentProvider::class, $paymentProvider);
    }

    /** @test */
    public function testCanInstantiateStripePaymentProviderThroughCallProxy()
    {
        $paymentProvider = call(StripePaymentProvider::class)->getInternal(Call::INSTANCE);

        $this->assertInstanceOf(StripePaymentProvider::class, $paymentProvider);
    }

    /** @test */
    public function testCanSetCashierCurrencyAs()
    {
        $paymentProvider = app(StripePaymentProvider::class);

        $paymentProvider->cashierCurrencyAs(
            new Currency('USD')
        );

        $this->assertStringContainsString('USD', config('cashier.currency'));
    }

    /** @test */
    public function testCanCreateSetupIntent()
    {
        $paymentProvider = app(StripePaymentProvider::class);

        $intent = $paymentProvider->setupIntentUsing(new User);

        $this->assertInstanceOf(SetupIntent::class, $intent);
    }

    /** @test */
    public function testCanMakeCustomerUsing()
    {
        $paymentProvider = app(StripePaymentProvider::class);

        $customer = $paymentProvider->makeCustomerUsing(new User);

        $this->assertInstanceOf(Customer::class, $customer);
    }

    /** @test */
    public function testCanSetPaymentMethodFor()
    {
        $paymentProvider = app(StripePaymentProvider::class);

        $paymentMethod = $paymentProvider->setPaymentMethodFor(
            new User,
            new PaymentMethod('test_id')
        );

        $this->assertInstanceOf(CashierPaymentMethod::class, $paymentMethod);
    }

    /** @test */
    public function testCanAttachPaymentMethodToCustomer()
    {
        $paymentProvider = app(StripePaymentProvider::class);

        $paymentMethod = $paymentProvider->attachPaymentMethodToCustomer(
            new PaymentMethodAttachmentData(
                paymentMethod: new PaymentMethod('test_id'),
                customer: new Customer('test_id'),
            )
        );

        $this->assertInstanceOf(PaymentMethod::class, $paymentMethod);
    }

    /** @test */
    public function testCanCreatePaymentIntent()
    {
        bind(PaymentIntentService::class)
            ->method()
            ->create(fn () => new PaymentIntent('test_id'));

        $paymentProvider = app(StripePaymentProvider::class);

        $paymentIntent = $paymentProvider->createPaymentIntent(
            new PaymentIntentData(
                paymentAmount: new StripePaymentAmount(100, 'PLN'),
                model: new User(['stripe_id' => 'test']),
            )
        );

        $this->assertInstanceOf(PaymentIntent::class, $paymentIntent);
    }

    /** @test */
    public function testCanConfirmPaymentIntent()
    {
        $paymentProvider = app(StripePaymentProvider::class);

        $confirmedPaymentIntent = $paymentProvider->confirmPaymentIntent(
            new PaymentIntentData(
                paymentIntent: new PaymentIntent('test_id'),
            )
        );

        $this->assertInstanceOf(PaymentIntent::class, $confirmedPaymentIntent);
    }

    /** @test */
    public function testCanUpdatePaymentIntent()
    {
        $paymentProvider = app(StripePaymentProvider::class);

        $updatedPaymentIntent = $paymentProvider->updatePaymentIntent(
            new PaymentIntentData(
                intentId: 'test_id',
                model: new User(['stripe_id' => 'test']),
                params: ['description' => 123],
            )
        );

        $this->assertInstanceOf(PaymentIntent::class, $updatedPaymentIntent);
    }

    /** @test */
    public function testCanRetrievePaymentIntent()
    {
        $paymentProvider = app(StripePaymentProvider::class);

        $updatedPaymentIntent = $paymentProvider->retrievePaymentIntent(
            new PaymentIntentData(
                intentId: 'test_id',
                params: ['description' => 123],
            )
        );

        $this->assertInstanceOf(PaymentIntent::class, $updatedPaymentIntent);
    }
}
