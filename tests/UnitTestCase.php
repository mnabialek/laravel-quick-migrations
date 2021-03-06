<?php

namespace Mnabialek\LaravelQuickMigrations\Tests;

use Carbon\Carbon;
use Mockery;
use PHPUnit\Framework\TestCase;

class UnitTestCase extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
        Carbon::setTestNow();
    }
}
