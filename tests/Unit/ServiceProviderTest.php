<?php

declare(strict_types=1);

namespace JsonMapper\LaravelPackage\Tests\Unit;

use Illuminate\Config\Repository;
use JsonMapper\JsonMapper;
use JsonMapper\LaravelPackage\ServiceProvider;
use PHPUnit\Framework\TestCase;

class ServiceProviderTest extends TestCase
{
    /**
     * @covers \JsonMapper\LaravelPackage\ServiceProvider
     */
    public function testBootPublishesConfig(): void
    {
        $app = new \Illuminate\Foundation\Application();
        $serviceProvider = new ServiceProvider($app);

        $serviceProvider->boot();

        self::assertArrayHasKey(ServiceProvider::class, $serviceProvider::$publishes);
        self::assertIsArray($serviceProvider::$publishes[ServiceProvider::class]);
        self::assertCount(1, $serviceProvider::$publishes[ServiceProvider::class]);
    }

    /**
     * @covers \JsonMapper\LaravelPackage\ServiceProvider
     */
    public function testRegisterMakesJsonMapperAvailableInApp(): void
    {
        $app = new \Illuminate\Foundation\Application();
        $app->offsetSet('config', new Repository());
        $serviceProvider = new ServiceProvider($app);


        $serviceProvider->register();

        self::assertTrue($app->has(JsonMapper::class));
        self::assertInstanceOf(JsonMapper::class, $app->make(JsonMapper::class));
    }
}
