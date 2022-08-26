<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Tests\Service;

use Lemonmind\SaveStringOperationsBundle\Tests\TestObject\TestObject;
use Pimcore\Test\KernelTestCase;
use ReflectionClass;

class StringConcatServiceTest extends KernelTestCase
{
    /**
     * @test
     * @dataProvider dataProvider
     *
     * @throws \ReflectionException
     */
    public function testStringConcat(
        string $name,
        string $description,
        array $fields,
        string $userInput,
        string $separator,
        string $fieldToSaveConcat,
        string $expected,
        int $productNumber
    ): void {
        $objectListing = [];

        for ($i = 0; $i < $productNumber; ++$i) {
            $objectListing[] = new TestObject($name, $description);
        }

        $reflector = new ReflectionClass('Lemonmind\SaveStringOperationsBundle\Services\StringConcatService');
        $method = $reflector->getMethod('stringConcat');
        $method->invokeArgs(null, [$objectListing, $fields, $userInput, $fieldToSaveConcat, $separator, false]);

        for ($i = 0; $i < $productNumber; ++$i) {
            /* @phpstan-ignore-next-line */
            $this->assertEquals($expected, $objectListing[$i]->get($fieldToSaveConcat));
        }
    }

    public function dataProvider(): array
    {
        return [
            ['lorem', 'some text', ['name', 'description'], '', ' ', 'name', 'lorem some text', 1],
            ['lorem', 'some text', ['name', 'description'], '', ' ', 'name', 'lorem some text', 10],
            ['lorem', 'some text', ['name', 'description'], '', ' ', 'description', 'lorem some text', 1],
            ['lorem', 'some text', ['name', 'description'], '', ' ', 'description', 'lorem some text', 10],
            ['lorem', 'some text', ['description', 'name'], '', ' ', 'name', 'some text lorem', 1],
            ['lorem', 'some text', ['description', 'name'], '', ' ', 'name', 'some text lorem', 10],
            ['lorem', 'some text', ['name', 'description'], '', ',', 'name', 'lorem,some text', 1],
            ['lorem', 'some text', ['name', 'description'], '', ',', 'name', 'lorem,some text', 10],
            ['lorem', 'some text', ['input', 'description'], 'input text', ' ', 'description', 'input text some text', 1],
            ['lorem', 'some text', ['input', 'description'], 'input text', ' ', 'description', 'input text some text', 10],
            ['lorem', 'some text', ['name', 'input'], 'input text', ' ', 'name', 'lorem input text', 1],
            ['lorem', 'some text', ['name', 'input'], 'input text', ' ', 'name', 'lorem input text', 10],
        ];
    }
}
