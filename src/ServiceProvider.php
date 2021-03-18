<?php

declare(strict_types=1);

namespace JsonMapper\LaravelPackage;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use JsonMapper\JsonMapper;
use JsonMapper\JsonMapperBuilder;
use JsonMapper\JsonMapperFactory;
use JsonMapper\JsonMapperInterface;

class ServiceProvider extends BaseServiceProvider
{
    private const CONFIG_FILE = __DIR__ . '/../config/json-mapper.php';

    /** @return void */
    public function register()
    {
        $this->mergeConfigFrom(self::CONFIG_FILE, 'json-mapper');

        $this->app->singleton(JsonMapperInterface::class, function ($app) {
            $config = $app->get('config')->get('json-mapper.type');
            $builder = JsonMapperBuilder::new()
                ->withJsonMapperClassName(\JsonMapper\LaravelPackage\JsonMapper::class);
            $factory = new JsonMapperFactory($builder);

            switch ($config) {
                case 'best-fit':
                    return $factory->bestFit();
                case 'default':
                default:
                    return $factory->default();
            }
        });

        $this->app->alias(JsonMapperInterface::class, JsonMapper::class);
        $this->app->alias(JsonMapperInterface::class, \JsonMapper\LaravelPackage\JsonMapperInterface::class);
        $this->app->alias(JsonMapperInterface::class, \JsonMapper\LaravelPackage\JsonMapper::class);
    }

    /**
     * @return void
     */
    public function boot()
    {
        $this->publishes([self::CONFIG_FILE => \config_path('json-mapper.php')]);
    }
}
