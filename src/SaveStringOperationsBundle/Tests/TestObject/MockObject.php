<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Tests\TestObject;

class MockObject
{
    private mixed $value;

    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    public function save(): void
    {
    }

    public static function setGetInheritedValues(bool $value): void
    {
    }
}
