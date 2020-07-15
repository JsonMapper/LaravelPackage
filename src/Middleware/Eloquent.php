<?php

declare(strict_types=1);

namespace JsonMapper\LaravelPackage\Middleware;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use JsonMapper\Builders\PropertyBuilder;
use JsonMapper\Enums\Visibility;
use JsonMapper\JsonMapperInterface;
use JsonMapper\Middleware\AbstractMiddleware;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\Wrapper\ObjectWrapper;
use Psr\SimpleCache\CacheInterface;

/**
 * Heavily based on the work of Barry vd. Heuvel in https://github.com/barryvdh/laravel-ide-helper
 */
class Eloquent extends AbstractMiddleware
{
    private const DOC_BLOCK_REGEX = '/@(?P<name>[A-Za-z_-]+)(?:[ \t]+(?P<value>.*?))?[ \t]*\r?$/m';

    /** @var CacheInterface */
    private $cache;
    /** @var string */
    private $dateClass;

    public function __construct(CacheInterface $cache)
    {
        if (! interface_exists('Doctrine\DBAL\Driver')) {
            throw new \Exception('Warning: `"doctrine/dbal": "~2.3"` is required to load database information. ' .
                'Please require that in your composer.json and run `composer update`.');
        }

        $this->cache = $cache;

        $this->dateClass = class_exists(\Illuminate\Support\Facades\Date::class)
            ? '\\' . get_class(\Illuminate\Support\Facades\Date::now())
            : '\Illuminate\Support\Carbon';
    }

    public function handle(
        \stdClass $json,
        ObjectWrapper $object,
        PropertyMap $propertyMap,
        JsonMapperInterface $mapper
    ): void {
        $inner = $object->getObject();
        if (! $inner instanceof Model) {
            return;
        }

        if ($this->cache->has($object->getName())) {
            $propertyMap->merge($this->cache->get($object->getName()));
        }

        $intermediatePropertyMap = $this->fetchPropertyMapForEloquent($inner);
        $this->cache->set($object->getName(), $intermediatePropertyMap);
        $propertyMap->merge($intermediatePropertyMap);
    }

    private function fetchPropertyMapForEloquent(Model $object): PropertyMap
    {
        $intermediatePropertyMap = new PropertyMap();

        /* Implement logic: properties, casts */
        $this->discoverPropertiesFromTable($object, $intermediatePropertyMap);
        if (method_exists($object, 'getCasts')) {
            $this->discoverPropertiesCasts($object, $intermediatePropertyMap);
        }

        return $intermediatePropertyMap;
    }

    protected function discoverPropertiesFromTable(Model $model, PropertyMap $propertyMap)
    {
        $table = $model->getConnection()->getTablePrefix() . $model->getTable();
        $schema = $model->getConnection()->getDoctrineSchemaManager($table);
        $databasePlatform = $schema->getDatabasePlatform();
        $databasePlatform->registerDoctrineTypeMapping('enum', 'string');

        $database = null;
        if (strpos($table, '.')) {
            list($database, $table) = explode('.', $table);
        }

        $columns = $schema->listTableColumns($table, $database);

        if (count($columns) === 0) {
            return;
        }

        foreach ($columns as $column) {
            $name = $column->getName();
            if (in_array($name, $model->getDates())) {
                $type = $this->dateClass;
            } else {
                $type = $column->getType()->getName();
                switch ($type) {
                    case 'string':
                    case 'text':
                    case 'date':
                    case 'time':
                    case 'guid':
                    case 'datetimetz':
                    case 'datetime':
                    case 'decimal':
                        $type = 'string';
                        break;
                    case 'integer':
                    case 'bigint':
                    case 'smallint':
                        $type = 'integer';
                        break;
                    case 'boolean':
                        switch (config('database.default')) {
                            case 'sqlite':
                            case 'mysql':
                                $type = 'integer';
                                break;
                            default:
                                $type = 'boolean';
                                break;
                        }
                        break;
                    case 'float':
                        $type = 'float';
                        break;
                    default:
                        $type = 'mixed';
                        break;
                }
            }

            $property = PropertyBuilder::new()
                ->setName($name)
                ->setType($type)
                ->setIsNullable(!$column->getNotnull())
                ->setVisibility(Visibility::PUBLIC())
                ->build();
            $propertyMap->addProperty($property);
        }
    }

    protected function discoverPropertiesCasts(Model $model, PropertyMap $propertyMap)
    {
        $casts = $model->getCasts();
        foreach ($casts as $name => $type) {
            switch ($type) {
                case 'boolean':
                case 'bool':
                    $realType = 'boolean';
                    break;
                case 'string':
                    $realType = 'string';
                    break;
                case 'array':
                case 'json':
                    $realType = 'array';
                    break;
                case 'object':
                    $realType = 'object';
                    break;
                case 'int':
                case 'integer':
                case 'timestamp':
                    $realType = 'integer';
                    break;
                case 'real':
                case 'double':
                case 'float':
                    $realType = 'float';
                    break;
                case 'date':
                case 'datetime':
                    $realType = $this->dateClass;
                    break;
                case 'collection':
                    $realType = '\Illuminate\Support\Collection';
                    break;
                default:
                    $realType = class_exists($type) ? ('\\' . $type) : 'mixed';
                    break;
            }

            if (! $propertyMap->hasProperty($name)) {
                continue;
            }

            $realType = $this->checkForCustomLaravelCasts($realType);

            $builder = $propertyMap->getProperty($name)->asBuilder();
            $property = $builder->setType($realType)->build();
            $propertyMap->addProperty($property);
        }
    }

    protected function checkForCustomLaravelCasts(string $type): ?string
    {
        if (!class_exists($type) || !interface_exists(CastsAttributes::class)) {
            return $type;
        }

        $reflection = new \ReflectionClass($type);

        if (!$reflection->implementsInterface(CastsAttributes::class)) {
            return $type;
        }

        $methodReflection = new \ReflectionMethod($type, 'get');

        return $this->getReturnTypeFromReflection($methodReflection)
            ?: $this->getReturnTypeFromDocBlock($methodReflection->getDocComment())
                ?: $type;
    }

    protected function getReturnTypeFromReflection(\ReflectionMethod $reflection): ?string
    {
        $returnType = $reflection->getReturnType();
        if (!$returnType) {
            return null;
        }

        $type = $returnType instanceof \ReflectionNamedType ? $returnType->getName() : (string)$returnType;

        if (!$returnType->isBuiltin()) {
            $type = '\\' . $type;
        }

        return $type;
    }

    private function getReturnTypeFromDocBlock(string $docBlock): ?string
    {
        // Strip away the start "/**' and ending "*/"
        if (strpos($docBlock, '/**') === 0) {
            $docBlock = substr($docBlock, 3);
        }
        if (substr($docBlock, -2) === '*/') {
            $docBlock = substr($docBlock, 0, -2);
        }
        $docBlock = trim($docBlock);

        $return = null;
        if (preg_match_all(self::DOC_BLOCK_REGEX, $docBlock, $matches)) {
            for ($x = 0, $max = count($matches[0]); $x < $max; $x++) {
                if ($matches['name'][$x] === 'return') {
                    $return = $matches['value'][$x];
                }
            }
        }

        return $return ?: null;
    }
}