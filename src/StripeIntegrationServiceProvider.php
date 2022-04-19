<?php

declare(strict_types=1);

namespace MichaelRubel\StripeIntegration;

use MichaelRubel\EnhancedContainer\LecServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
        $package->name('laravel-stripe-integration');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function packageRegistered(): void
    {
        $this->app->register(LecServiceProvider::class);
    }
}
