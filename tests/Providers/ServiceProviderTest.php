<?php

namespace Mnabialek\LaravelQuickMigrations\Tests\Providers;

use ArrayAccess;
use Illuminate\Container\Container;
use Mnabialek\LaravelQuickMigrations\Providers\ServiceProvider;
use Mnabialek\LaravelQuickMigrations\Tests\UnitTestCase;
use Mockery;

class ServiceProviderTest extends UnitTestCase
{
    /** @test */
    public function it_merges_config_and_publishes_file()
    {
        $app = Mockery::mock(Container::class, ArrayAccess::class);
        Container::setInstance($app);

        $provider = Mockery::mock(ServiceProvider::class)->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $provider->__construct($app);

        $baseDir = '/some/sample/directory';

        $app->shouldReceive('configPath')->atLeast()->once()
            ->with('quick_migrations.php')->andReturn($baseDir.'/quick_migrations.php');

        $configFile = realpath(__DIR__ . '/../../publish/config/quick_migrations.php');
        $provider->shouldReceive('mergeConfigFrom')->once()->with(
            $configFile,
            'quick_migrations'
        );

        $provider->shouldReceive('publishes')->once()->with(
            [$configFile => config_path('quick_migrations.php')]
        );

        $provider->register();
        $this->assertTrue(true);
    }
}
