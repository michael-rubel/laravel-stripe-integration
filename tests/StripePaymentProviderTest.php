<?php

namespace MichaelRubel\StripeIntegration\Tests;

use Laravel\Cashier\PaymentMethod as CashierPaymentMethod;
use MichaelRubel\EnhancedContainer\Call;
use MichaelRubel\StripeIntegration\Providers\StripePaymentProvider;
use MichaelRubel\StripeIntegration\Tests\Stubs\User;
use Money\Currency;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\SetupIntent;

class StripePaymentProviderTest extends TestCase
{
    /** @test */
    public function testCanInstantiateStripePaymentProvider()
    {
        $paymentProvider = new StripePaymentProvider;

        $this->assertInstanceOf(StripePaymentProvider::class, $paymentProvider);
    }

    /** @test */
    public function testCanInstantiateStripePaymentProviderThroughCallProxy()
    {
        $paymentProvider = call(StripePaymentProvider::class)->getInternal(Call::INSTANCE);

        $this->assertInstanceOf(StripePaymentProvider::class, $paymentProvider);
    }

    /** @test */
    public function testCanConfigureCashierCurrency()
    {
        $paymentProvider = new StripePaymentProvider;

        $paymentProvider->configureCashierCurrency(
            new Currency('USD')
        );

        $this->assertStringContainsString('USD', config('cashier.currency'));
    }

    /** @test */
    public function testCanCreateSetupIntent()
    {
        $paymentProvider = new StripePaymentProvider;

        $intent = $paymentProvider->setupIntentUsing(new User);

        $this->assertInstanceOf(SetupIntent::class, $intent);
    }

    /** @test */
    public function testCanPrepareCustomer()
    {
        $paymentProvider = new StripePaymentProvider;

        $customer = $paymentProvider->prepareCustomer(new User);

        $this->assertInstanceOf(Customer::class, $customer);
    }

    /** @test */
    public function testCanUpdatePaymentMethod()
    {
        $paymentProvider = new StripePaymentProvider;

        $paymentMethod = $paymentProvider->updatePaymentMethod(
            new User,
            new PaymentMethod('test_id')
        );

        $this->assertInstanceOf(CashierPaymentMethod::class, $paymentMethod);
    }

    /** @test */
    public function testCanAttachPaymentMethodToCustomer()
    {
        $paymentProvider = new StripePaymentProvider;

        $paymentMethod = $paymentProvider->attachPaymentMethodToCustomer(
            new PaymentMethod('test_id'),
            new Customer('test_id')
        );

        $this->assertInstanceOf(PaymentMethod::class, $paymentMethod);
    }
}
