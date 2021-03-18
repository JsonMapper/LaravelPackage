<?php

declare(strict_types=1);

namespace JsonMapper\LaravelPackage\Tests\Implementation\ChuckNorris;


class Joke
{
    /** @var string[] */
    public $categories;
    /** @var \DateTimeImmutable */
    public $created_at;
    /** @var string */
    public $icon_url;
    /** @var string */
    public $id;
    /** @var \DateTimeImmutable */
    public $updated_at;
    /** @var string */
    public $url;
    /** @var string */
    public $value;
}