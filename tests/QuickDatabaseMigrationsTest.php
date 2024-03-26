<?php

namespace Mnabialek\LaravelQuickMigrations\Tests;

use ArrayAccess;
use Illuminate\Container\Container;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Foundation\Testing\TestCase;
use Mnabialek\LaravelQuickMigrations\QuickDatabaseMigrations;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use stdClass;

class QuickDatabaseMigrationsTest extends UnitTestCase
{
    #[Test]
    public function it_runs_default_method_when_turned_off()
    {
        $app = Mockery::mock(Container::class, ArrayAccess::class);

        $config = Mockery::mock(stdClass::class);

        $app->shouldReceive('make')->once()->with('config', [])->andReturn($config);

        $config->shouldReceive('get')->once()->with('quick_migrations.enabled', null)->andReturn(false);

        Container::setInstance($app);

        $class = Mockery::mock(TestClass::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $class->shouldReceive('useDefaultWay')->once()->withNoArgs();
        $class->shouldNotReceive('artisan');
        $class->shouldNotReceive('beforeApplicationDestroyed');

        $class->runDatabaseMigrations();
        $this->assertTrue(true);
    }

    #[Test]
    public function it_runs_custom_code_when_turned_on()
    {
        $app = Mockery::mock(Container::class, ArrayAccess::class);

        $config = Mockery::mock(stdClass::class);

        $app->shouldReceive('make')->once()->with('config', [])->andReturn($config);

        $config->shouldReceive('get')->once()->with('quick_migrations.enabled', null)->andReturn(true);

        Container::setInstance($app);

        $class = Mockery::mock(TestClass::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $class->setApp($app);

        $class->shouldNotReceive('useDefaultWay');

        $class->shouldReceive('artisan')->once()->with('migrate:fresh', [
            '--path' => realpath(__DIR__ . '/../migrations'),
            '--realpath' => 1,
        ]);

        $kernel = Mockery::mock(stdClass::class);

        $app->shouldReceive('offsetGet')->once()->with(Kernel::class)->andReturn($kernel);

        $kernel->shouldReceive('setArtisan')->once()->with(null);

        // set some state
        RefreshDatabaseState::$migrated = true;
        $this->assertTrue(RefreshDatabaseState::$migrated);

        $class->shouldReceive('beforeApplicationDestroyed')->once()->passthru();

        $class->runDatabaseMigrations();

        $callbacks = $class->getCallbacks();
        $this->assertCount(1, $callbacks);

        // execute callback
        $callbacks[0]();

        // make sure state was changed
        $this->assertFalse(RefreshDatabaseState::$migrated);
    }
}

class TestClass extends TestCase
{
    use QuickDatabaseMigrations;

    public function setApp($app)
    {
        $this->app = $app;
    }

    public function createApplication()
    {
    }

    public function getCallbacks()
    {
        return $this->beforeApplicationDestroyedCallbacks;
    }
}
