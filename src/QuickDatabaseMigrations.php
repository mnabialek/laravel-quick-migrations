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
            $this->runDefaultDatabaseMigrations();

            return;
        }

        // otherwise we just run single migration that loads database dump
        $this->artisan('migrate:fresh', ['--path' => realpath('/../../migrations')]);

        $this->app[Kernel::class]->setArtisan(null);

        // here we don't care about rollback (to make it faster)
        $this->beforeApplicationDestroyed(function () {
            RefreshDatabaseState::$migrated = false;
        });
    }
}
