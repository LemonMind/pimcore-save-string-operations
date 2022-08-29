<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Tests\TestObject;

class TestObject
{
    private string $name;
    private string $description;
    private float $price;

    public function __construct(string $name, string $description, float $price)
    {
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
    }

    public function get(string $field): string|float
    {
        if ('name' === $field) {
            return $this->name;
        }

        if ('description' === $field) {
            return $this->description;
        }

        return $this->price;
    }

    public function set(string $field, mixed $value): void
    {
        if ('name' === $field) {
            $this->name = $value;
        } elseif ('description' === $field) {
            $this->description = $value;
        } else {
            $this->price = $value;
        }
    }

    public function save(): void
    {
    }

    public static function setGetInheritedValues(bool $value): void
    {
    }
}
