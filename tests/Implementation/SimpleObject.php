<?php

declare(strict_types=1);

namespace JsonMapper\LaravelPackage\Tests\Implementation;


class SimpleObject
{
    /** @var int */
    private $number;

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    public static function withNumber(int $number): self
    {
        $instance = new self();
        $instance->setNumber($number);

        return $instance;
    }
}