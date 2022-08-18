<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringReplaceBundle\Tests\Controller;

use Lemonmind\SaveStringReplaceBundle\Controller\StringController;
use Lemonmind\SaveStringReplaceBundle\Tests\TestObject\TestObject;
use Pimcore\Test\KernelTestCase;
use ReflectionClass;

class StringControllerTest extends KernelTestCase
{
    private array $objectListing;

    /**
     * @test
     * @dataProvider dataProvider
     *
     * @throws \ReflectionException
     */
    public function testStringReplace(string $name, string $search, string $replace, string $expected, bool $isInsensitive, int $productNumber): void
    {
        for ($i = 0; $i < $productNumber; ++$i) {
            $this->objectListing[] = new TestObject($name);
        }
        $this->objectListing[] = new TestObject('different name');
        $controller = new StringController();
        $reflector = new ReflectionClass($controller);

        $reflector->getProperty('field')->setValue($controller, 'name');
        $reflector->getProperty('search')->setValue($controller, $search);
        $reflector->getProperty('replace')->setValue($controller, $replace);
        $reflector->getProperty('isInsensitive')->setValue($controller, $isInsensitive);

        $method = $reflector->getMethod('stringReplace');
        $method->invokeArgs($controller, [$this->objectListing]);

        for ($i = 0; $i < $productNumber - 1; ++$i) {
            $this->assertEquals($expected, $this->objectListing[$i]->get());
        }
        $this->assertEquals('different name', $this->objectListing[$productNumber]->get());
    }

    public function dataProvider(): array
    {
        return [
            ['lorem', 'lorem', 'ipsum', 'ipsum', false, 1],
            ['lorem', 'lorem', 'ipsum', 'ipsum', false, 5],
            ['lorem', 'lorem', 'ipsum', 'ipsum', false, 40],
            ['lorem ipsum', 'lorem', 'ipsum', 'ipsum ipsum', false, 10],
            ['loREm ipsum', 'lorem', 'ipsum', 'ipsum ipsum', true, 10],
            ['loREm ipsum', 'LOREm', 'ipsum', 'ipsum ipsum', true, 10],
        ];
    }
}
