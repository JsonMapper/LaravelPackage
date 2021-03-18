<?php

declare(strict_types=1);

namespace JsonMapper\LaravelPackage\Tests\Integration;

use JsonMapper\LaravelPackage\JsonMapperInterface;
use JsonMapper\LaravelPackage\ServiceProvider;
use JsonMapper\LaravelPackage\Tests\Implementation\ChuckNorris\Joke;
use \Orchestra\Testbench\TestCase;

class JsonMapperTests extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    /** @covers \JsonMapper\LaravelPackage\JsonMapper */
    public function testJsonMapperCanMapToCollection(): void
    {
        /** @var JsonMapperInterface $mapper */
        $mapper = $this->app->make(JsonMapperInterface::class);
        $url = "https://api.chucknorris.io/jokes/search?query=save";
        $data = (string) file_get_contents($url);
        $json = json_decode($data, false);
        $jokes = $mapper->mapToCollection($json->result, new Joke());

        self::assertContainsOnlyInstancesOf(Joke::class, $jokes->all());
    }

    /** @covers \JsonMapper\LaravelPackage\JsonMapper */
    public function testJsonMapperCanMapToCollectionWithLargeJson(): void
    {
        /** @var JsonMapperInterface $mapper */
        $mapper = $this->app->make(JsonMapperInterface::class);
        $url = "https://api.chucknorris.io/jokes/search?query=kick";
        $data = (string) file_get_contents($url);
        $json = json_decode($data, false);
        $jokes = $mapper->mapToCollection($json->result, new Joke());

        self::assertContainsOnlyInstancesOf(Joke::class, $jokes->all());
    }

}