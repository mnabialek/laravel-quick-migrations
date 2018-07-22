<?php

namespace Mnabialek\LaravelQuickMigrations\Providers;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // merge config
        $this->mergeConfigFrom(realpath(__DIR__ . '/../../publish/config/quick_migrations.php'), 'quick_migrations');
    }
}
