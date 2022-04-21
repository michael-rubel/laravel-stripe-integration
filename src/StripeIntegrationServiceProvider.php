<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration;

use MichaelRubel\EnhancedContainer\LecServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stripe\StripeClient;

class StripeIntegrationServiceProvider extends PackageServiceProvider
{
    /**
     * Configure the package.
     *
     * @param Package $package
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
        $this->app->register(LecServiceProvider::class);

        bind(StripeClient::class)->to(new StripeClient(
            config('stripe-integration.secret')
        ));
    }
}
