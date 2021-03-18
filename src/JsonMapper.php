<?php

declare(strict_types=1);

namespace JsonMapper\LaravelPackage;

use Illuminate\Support\Collection;

class JsonMapper extends \JsonMapper\JsonMapper implements JsonMapperInterface
{
    public function mapToCollection(array $json, object $object): Collection
    {
        return collect($this->mapArray($json, $object));
    }

    public function mapToCollectionFromString(string $json, object $object): Collection
    {
        return collect($this->mapArrayFromString($json, $object));
    }
}