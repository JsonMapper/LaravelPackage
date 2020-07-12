<?php

declare(strict_types=1);

namespace JsonMapper\LaravelPackage;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use JsonMapper\JsonMapper;
use JsonMapper\JsonMapperFactory;
use JsonMapper\JsonMapperInterface;

class ServiceProvider extends BaseServiceProvider
{
    private const CONFIG_FILE = __DIR__ . '/../config/json-mapper.php';

    /**
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(self::CONFIG_FILE, 'json-mapper');

        $this->app->singleton(JsonMapperInterface::class, function ($app) {
            $config = $app->get('config')->get('json-mapper.type');
            switch ($config) {
                case 'best-fit':
                    return (new JsonMapperFactory())->bestFit();
                case 'default':
                default:
                    return (new JsonMapperFactory())->default();
            }
        });

        $this->app->alias(JsonMapperInterface::class, JsonMapper::class);
    }

    /**
     * @return void
     */
    public function boot()
    {
        $this->publishes([self::CONFIG_FILE => \config_path('json-mapper.php')]);
    }
}
