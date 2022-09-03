## Usage example
```php
/*
|--------------------------------------------------------------------------
| Notes
|--------------------------------------------------------------------------
| Don't copy & paste the code below. It's only an example flow
| definition. You should change the code based on your needs.
|
| If you wonder what is the `CallProxy`, it is used for method binding,
| i.e. you can use this for method mocking through the Service Container.
| https://github.com/michael-rubel/laravel-enhanced-container#method-binding
*/

class StripeCharge implements Action
{
    /**
     * @var CallProxy
     */
    private CallProxy $paymentProvider;

    /**
     * @param PaymentProviderContract $paymentProvider
     */
    public function __construct(PaymentProviderContract $paymentProvider)
    {
        $this->paymentProvider = call($paymentProvider);
    }

    /**
     * Execute the job.
     *
     * @return mixed
     */
    public function handle(): mixed
    {
        $currency = new Currency('USD');

        $this->paymentProvider->setCashierCurrency($currency);

        $customer = $this->paymentProvider->makeCustomerUsing(auth()->user());

        $paymentMethod = $this->paymentProvider->setPaymentMethodFor(auth()->user(), 'payment_method');

        $this->paymentProvider->attachPaymentMethodToCustomer(
            new PaymentMethodAttachmentData(
                paymentMethod: $paymentMethod,
                customer: $customer,
            )
        );

        $amount = app(PaymentAmount::class, [
            PaymentAmount::AMOUNT   => 1000,
            PaymentAmount::CURRENCY => $currency->getCode(),
        ]);

        $chargeData = new StripeChargeData(
            model: auth()->user(),
            payment_amount: $amount,
            payment_method: $paymentMethod,
            options: ['description' => 'Your description.'],
        );

        $payment = $this->paymentProvider->charge($chargeData);

        // Now you can check $payment->status
    }
}
```
