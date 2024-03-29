<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stripe\StripeClient;

class StripeIntegrationServiceProvider extends PackageServiceProvider
{
    /**
     * Configure the package.
     *
     * @param  Package  $package
     *
     * @return void
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-stripe-integration')
            ->hasConfigFile();
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function packageRegistered(): void
    {
        $this->app->bind(StripeClient::class, fn () => new StripeClient(
            config('stripe-integration.secret', env('STRIPE_SECRET'))
        ));
    }
}
