<?php

declare(strict_types=1);

namespace JsonMapper\LaravelPackage;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use JsonMapper\JsonMapper;
use JsonMapper\JsonMapperFactory;

class ServiceProvider extends BaseServiceProvider
{
    private const CONFIG_FILE = __DIR__ . '/../config/json-mapper.php';

    /**
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(self::CONFIG_FILE, 'json-mapper');

        switch (config('json-mapper.type')) {
            case 'best-fit':
                $this->app->singleton(JsonMapper::class, function () {
                    return (new JsonMapperFactory())->bestFit();
                });
                break;
            case 'default':
            default:
                $this->app->singleton(JsonMapper::class, function () {
                    return (new JsonMapperFactory())->default();
                });
                break;
        }
    }

    /**
     * @return void
     */
    public function boot()
    {
        $this->publishes([self::CONFIG_FILE => \config_path('json-mapper.php')]);
    }
}
