<?php

namespace MichaelRubel\StripeIntegration\Tests;

use MichaelRubel\StripeIntegration\Decorators\StripePaymentAmountDecorator;
use Money\Money;

class AmountDecoratorTest extends TestCase
{
    /** @test */
    public function testCanInstantiateDecorator()
    {
        $decorator = new StripePaymentAmountDecorator(5000, 'usd');

        $this->assertInstanceOf(Money::class, $decorator->money);
        $this->assertSame(500000, $decorator->getAmount());
        $this->assertSame(5000.0, $decorator->rawAmount);
        $this->assertSame('usd', $decorator->getCurrency()->getCode());
        $this->assertSame(100, $decorator->multiplier);
    }

    /** @test */
    public function testThrowsDivisionByZeroError()
    {
        $this->expectException(\DivisionByZeroError::class);

        new StripePaymentAmountDecorator(20000, 'pln', 0);
    }

    /** @test */
    public function testCanConvertToSmallestCommonCurrencyUnit()
    {
        $decorator = new StripePaymentAmountDecorator(1000, 'pln');

        $converted = $decorator->toPaymentSystemUnits();

        $this->assertSame(100000, $converted);
    }

    /** @test */
    public function testCanRevertFromSmallestCommonCurrencyUnit()
    {
        $decorator = new StripePaymentAmountDecorator(1005.49, 'usd');

        $this->assertSame(100549, $decorator->toPaymentSystemUnits());

        $reverted = $decorator->fromPaymentSystemUnits();

        $this->assertSame(1005.49, $reverted);
    }

    /** @test */
    public function testCanForwardCallToMoneyObject()
    {
        $decorator = new StripePaymentAmountDecorator(5000, 'usd');

        $this->assertSame(250000.0, (float) $decorator->divide(2)->getAmount());
    }
}
