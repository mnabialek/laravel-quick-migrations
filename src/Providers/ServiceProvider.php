<?php

namespace Mnabialek\LaravelQuickMigrations\Providers;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register()
    {
        // merge config
        $this->mergeConfigFrom($this->configFileLocation(), 'quick_migrations');

        // register files to be published
        $this->publishes([
            $this->configFileLocation() => config_path('quick_migrations.php'),
        ]);
    }

    /**
     * Get config file location.
     *
     * @return bool|string
     */
    protected function configFileLocation()
    {
        return realpath(__DIR__ . '/../../publish/config/quick_migrations.php');
    }
}
