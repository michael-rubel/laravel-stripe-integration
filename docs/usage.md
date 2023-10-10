## Usage example
```php
/*
|--------------------------------------------------------------------------
| Notes
|--------------------------------------------------------------------------
| Don't copy & paste the code below. It's only an example flow
| definition. You should change the code based on your needs.
|
| If you wonder what is the `CallProxy`, it is a method binding mechanism,
| i.e. you can use this to mock methods through the Service Container.
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
        $this->paymentProvider->setCashierCurrencyAs(new Currency('USD'));

        $customer = $this->paymentProvider->makeCustomerUsing(auth()->user());

        $paymentMethod = $this->paymentProvider->setPaymentMethodFor(auth()->user(), 'payment_method');

        $this->paymentProvider->attachPaymentMethodToCustomer(
            new PaymentMethodAttachmentData(
                paymentMethod: $paymentMethod,
                customer: $customer,
            )
        );

        $amount = new StripePaymentAmount(
            amount: 1000,
            currency: config('cashier.currency'),
        );

        $chargeData = new StripeChargeData(
            model: auth()->user(),
            paymentAmount: $amount,
            paymentMethod: $paymentMethod,
            options: ['description' => 'Your nice description.'],
        );

        $payment = $this->paymentProvider->charge($chargeData);

        // you can check $payment->status now
    }
}
```
