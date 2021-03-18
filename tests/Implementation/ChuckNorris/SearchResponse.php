<?php

declare(strict_types=1);

namespace JsonMapper\LaravelPackage\Tests\Implementation\ChuckNorris;


class SearchResponse
{
    /** @var int */
    public $total;
    /** @var Joke[]  */
    public $result;
}