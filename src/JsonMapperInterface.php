<?php

declare(strict_types=1);

namespace JsonMapper\LaravelPackage;

use Illuminate\Support\Collection;

interface JsonMapperInterface extends \JsonMapper\JsonMapperInterface
{
    public function mapToCollection(array $json, object $object): Collection;

    public function mapToCollectionFromString(string $json, object $object): Collection;
}