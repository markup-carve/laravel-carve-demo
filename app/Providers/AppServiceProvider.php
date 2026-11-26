<?php

namespace App\Providers;

use App\Carve\ConfigIncludeResolver;
use Illuminate\Support\ServiceProvider;
use MarkupCarve\LaravelCarve\Service\CarveConverter;
use MarkupCarve\LaravelCarve\Service\CarveManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            ConfigIncludeResolver::class,
            static fn (): ConfigIncludeResolver => new ConfigIncludeResolver(config('carve.include_snippets', [])),
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // A profile customizer: the config_snippets converter resolves includes from configuration.
        $this->app->afterResolving(CarveManager::class, function (CarveManager $manager): void {
            $manager->extend('config_snippets', function (CarveConverter $converter): void {
                $converter->setOption('includes', [
                    'resolver' => $this->app->make(ConfigIncludeResolver::class),
                    'file' => 'config:account-overview',
                ]);
            });
        });
    }
}
