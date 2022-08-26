<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Tests\TestObject;

class TestObject
{
    private string $name;
    private string $description;

    public function __construct(string $name, string $description)
    {
        $this->name = $name;
        $this->description = $description;
    }

    public function get(string $field): string
    {
        if ('name' === $field) {
            return $this->name;
        }

        return $this->description;
    }

    public function set(string $field, string $text): void
    {
        if ('name' === $field) {
            $this->name = $text;
        } else {
            $this->description = $text;
        }
    }

    public function save(): void
    {
    }

    public static function setGetInheritedValues(bool $value): void
    {
    }
}
