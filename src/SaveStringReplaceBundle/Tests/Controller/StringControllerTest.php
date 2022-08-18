<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringReplaceBundle\Tests\Controller;

use Lemonmind\SaveStringReplaceBundle\Controller\StringController;
use Pimcore\Test\KernelTestCase;
use ReflectionClass;

class TestProduct
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

class StringControllerTest extends KernelTestCase
{
    private array $productListing;

    /**
     * @test
     * @dataProvider dataProvider
     *
     * @throws \ReflectionException
     */
    public function testStringReplace(string $name, string $search, string $replace, string $expected, bool $isInsensitive, int $productNumber): void
    {
        for ($i = 0; $i < $productNumber; ++$i) {
            $this->productListing[] = new TestProduct($name);
        }
        $this->productListing[] = new TestProduct('different name');
        $controller = new StringController();
        $reflector = new ReflectionClass($controller);

        $reflector->getProperty('field')->setValue($controller, 'name');
        $reflector->getProperty('search')->setValue($controller, $search);
        $reflector->getProperty('replace')->setValue($controller, $replace);
        $reflector->getProperty('isInsensitive')->setValue($controller, $isInsensitive);

        $method = $reflector->getMethod('stringReplace');
        $method->invokeArgs($controller, [$this->productListing]);

        for ($i = 0; $i < $productNumber - 1; ++$i) {
            $this->assertEquals($expected, $this->productListing[$i]->get());
        }
        $this->assertEquals('different name', $this->productListing[$productNumber]->get());
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
