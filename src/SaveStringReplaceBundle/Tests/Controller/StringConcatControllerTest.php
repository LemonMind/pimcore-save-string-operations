<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringReplaceBundle\Tests\Controller;

use Lemonmind\SaveStringReplaceBundle\Controller\StringConcatController;
use Lemonmind\SaveStringReplaceBundle\Tests\TestObject\TestObject;
use Pimcore\Test\KernelTestCase;
use ReflectionClass;

class StringConcatControllerTest extends KernelTestCase
{
    private array $objectListing;

    /**
     * @test
     * @dataProvider dataProvider
     *
     * @throws \ReflectionException
     */
    public function testStringConcat(string $name, string $description, array $fields, string $userInput, string $separator, string $fieldToSaveConcat, string $expected, int $productNumber): void
    {
        for ($i = 0; $i < $productNumber; ++$i) {
            $this->objectListing[] = new TestObject($name, $description);
        }
        $controller = new StringConcatController();
        $reflector = new ReflectionClass($controller);

        $reflector->getProperty('fields')->setValue($controller, $fields);
        $reflector->getProperty('userInput')->setValue($controller, $userInput);
        $reflector->getProperty('fieldToSaveConcat')->setValue($controller, $fieldToSaveConcat);
        $reflector->getProperty('separator')->setValue($controller, $separator);

        $method = $reflector->getMethod('stringConcat');
        $method->invokeArgs($controller, [$this->objectListing]);

        for ($i = 0; $i < $productNumber; ++$i) {
            $this->assertEquals($expected, $this->objectListing[$i]->get($fieldToSaveConcat));
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
