<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringReplaceBundle\Tests\TestObject;

class TestObject
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function get(): string
    {
        return $this->name;
    }

    public function set(string $field, string $name): void
    {
        $this->name = $name;
    }

    public function save(): void
    {
    }
}
