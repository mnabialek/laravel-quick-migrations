<?php

namespace Mnabialek\LaravelQuickMigrations;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabaseState;

trait QuickDatabaseMigrations
{
    use DatabaseMigrations {
        runDatabaseMigrations as runDefaultDatabaseMigrations;
    }

    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */
    public function runDatabaseMigrations()
    {
        // if disabled we just run parent method and that's it
        if (! config('quick_migrations.enabled')) {
            $this->useDefaultWay();

            return;
        }

        // otherwise we just run single migration that loads database dump
        $this->artisan('migrate:fresh', [
            '--path' => realpath(__DIR__ . '/../migrations'),
            '--realpath' => 1,
        ]);

        $this->app[Kernel::class]->setArtisan(null);

        // here we don't care about rollback (to make it faster)
        $this->beforeApplicationDestroyed(function () {
            RefreshDatabaseState::$migrated = false;
        });
    }

    /**
     * Use default handling way (same as DatabaseMigrations).
     * @codeCoverageIgnore
     */
    protected function useDefaultWay()
    {
        $this->runDefaultDatabaseMigrations();
    }
}
