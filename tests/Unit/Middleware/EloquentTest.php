<?php

declare(strict_types=1);

namespace JsonMapper\LaravelPackage\Tests\Unit\Middleware;


use JsonMapper\Cache\NullCache;
use JsonMapper\LaravelPackage\Middleware\Eloquent;
use JsonMapper\Wrapper\ObjectWrapper;
use Orchestra\Testbench\TestCase;

class EloquentTest extends TestCase
{
    /**
     * @covers \JsonMapper\LaravelPackage\Middleware\Eloquent
     */
    public function testName(): void
    {
        $middleware = new Eloquent(new NullCache());

        $middleware->handle(new \stdClass(), new ObjectWrapper());
    }

}