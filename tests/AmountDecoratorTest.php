<?php

namespace MichaelRubel\StripeIntegration\Tests;

use MichaelRubel\StripeIntegration\Decorators\StripePaymentAmount;
use Money\Money;

class AmountDecoratorTest extends TestCase
{
    /** @test */
    public function testCanInstantiateDecorator()
    {
        $decorator = new StripePaymentAmount(100, 'USD');

        $this->assertInstanceOf(StripePaymentAmount::class, $decorator);
    }

    /** @test */
    public function testThrowsDivisionByZeroError()
    {
        $this->expectException(\DivisionByZeroError::class);

        new StripePaymentAmount(20000, 'pln', 0);
    }

    /** @test */
    public function testThrowsInvalidArgumentWhenCurrencyIsEmpty()
    {
        $this->expectException(\InvalidArgumentException::class);

        new StripePaymentAmount(10, '');
    }

    /** @test */
    public function testCanConvertToSmallestCommonCurrencyUnit()
    {
        $decorator = new StripePaymentAmount(1005, 'pln');
        $converted = $decorator->toPaymentSystemUnits();
        $this->assertSame(100500, $converted);

        $decorator = new StripePaymentAmount(1005.50, 'pln');
        $converted = $decorator->toPaymentSystemUnits();
        $this->assertSame(100550, $converted);

        $decorator = new StripePaymentAmount('1005.550', 'pln');
        $converted = $decorator->toPaymentSystemUnits();
        $this->assertSame(100555, $converted);

        $decorator = new StripePaymentAmount('153000.777', 'pln');
        $converted = $decorator->toPaymentSystemUnits();
        $this->assertSame(15300077, $converted);
    }

    /** @test */
    public function testCanRevertFromSmallestCommonCurrencyUnit()
    {
        $decorator = new StripePaymentAmount(1005.49, 'usd');

        $this->assertSame(100549, $decorator->toPaymentSystemUnits());

        $reverted = $decorator->fromPaymentSystemUnits();

        $this->assertSame(1005.49, $reverted);
    }

    /** @test */
    public function testCanForwardCallToMoneyObject()
    {
        $decorator = new StripePaymentAmount(5000, 'usd');

        $this->assertSame(250000.0, (float) $decorator->divide(2)->getAmount());
    }

    /** @test */
    public function testMainAssertionsAreSuccessful()
    {
        $decorator = new StripePaymentAmount(1090.7, 'USD');

        $this->assertInstanceOf(Money::class, $decorator->money);
        $this->assertSame(109070, $decorator->getAmount());
        $this->assertSame(1090.7, $decorator->amount);
        $this->assertSame('USD', $decorator->getCurrency()->getCode());
        $this->assertSame(100, $decorator->multiplier);

        $decorator = new StripePaymentAmount(1005.50, 'PLN');

        $this->assertInstanceOf(Money::class, $decorator->money);
        $this->assertSame(100550, $decorator->getAmount());
        $this->assertSame(1005.50, $decorator->amount);
        $this->assertSame('PLN', $decorator->getCurrency()->getCode());
        $this->assertSame(100, $decorator->multiplier);

        $decorator = new StripePaymentAmount('1005.50', 'GBP');

        $this->assertInstanceOf(Money::class, $decorator->money);
        $this->assertSame(100550, $decorator->getAmount());
        $this->assertSame(1005.50, $decorator->amount);
        $this->assertSame('GBP', $decorator->getCurrency()->getCode());
        $this->assertSame(100, $decorator->multiplier);

        $decorator = new StripePaymentAmount('1005', 'UAH');

        $this->assertInstanceOf(Money::class, $decorator->money);
        $this->assertSame(100500, $decorator->getAmount());
        $this->assertSame(1005.0, $decorator->amount);
        $this->assertSame('UAH', $decorator->getCurrency()->getCode());
        $this->assertSame(100, $decorator->multiplier);
    }
}
