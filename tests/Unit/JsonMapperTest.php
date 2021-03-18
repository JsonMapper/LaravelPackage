<?php

declare(strict_types=1);

namespace JsonMapper\LaravelPackage\Tests\Unit;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use JsonMapper\LaravelPackage\JsonMapperInterface;
use JsonMapper\LaravelPackage\ServiceProvider;
use JsonMapper\LaravelPackage\Tests\Implementation\SimpleObject;
use PHPUnit\Framework\TestCase;

class JsonMapperTest extends TestCase
{
    /** @covers \JsonMapper\LaravelPackage\JsonMapper */
    public function testCanMapToCollection(): void
    {
        $app = new Application();
        $app->offsetSet('config', new Repository(['json-mapper.type' => 'best-fit']));
        $serviceProvider = new ServiceProvider($app);

        $serviceProvider->boot();
        $serviceProvider->register();
        /** @var JsonMapperInterface $jsonMapper */
        $jsonMapper = $app->make(JsonMapperInterface::class);
        $data = json_decode('[{"number": 12}, {"number": 13}]', false);

        $result = $jsonMapper->mapToCollection($data, new SimpleObject());

        self::assertCount(2, $result);
        self::assertEquals(collect([SimpleObject::withNumber(12), SimpleObject::withNumber(13)]), $result);
    }

    /** @covers \JsonMapper\LaravelPackage\JsonMapper */
    public function testCanMapToCollectionFromstring(): void
    {
        $app = new Application();
        $app->offsetSet('config', new Repository(['json-mapper.type' => 'best-fit']));
        $serviceProvider = new ServiceProvider($app);

        $serviceProvider->boot();
        $serviceProvider->register();
        /** @var JsonMapperInterface $jsonMapper */
        $jsonMapper = $app->make(JsonMapperInterface::class);

        $result = $jsonMapper->mapToCollectionFromString('[{"number": 12}, {"number": 13}]', new SimpleObject());

        self::assertCount(2, $result);
        self::assertEquals(collect([SimpleObject::withNumber(12), SimpleObject::withNumber(13)]), $result);
    }
}