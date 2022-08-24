<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Tests\Controller;

use Lemonmind\SaveStringOperationsBundle\Controller\StringReplaceController;
use Lemonmind\SaveStringOperationsBundle\Tests\TestObject\TestObject;
use Pimcore\Test\KernelTestCase;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;

class StringReplaceControllerTest extends KernelTestCase
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
            $this->objectListing[] = new TestObject($name, '');
        }
        $this->objectListing[] = new TestObject('different name', '');
        $controller = new StringReplaceController();
        $reflector = new ReflectionClass($controller);

        $reflector->getProperty('field')->setValue($controller, 'name');
        $reflector->getProperty('search')->setValue($controller, $search);
        $reflector->getProperty('replace')->setValue($controller, $replace);
        $reflector->getProperty('isInsensitive')->setValue($controller, $isInsensitive);

        $method = $reflector->getMethod('stringReplace');
        $method->invokeArgs($controller, [$this->objectListing]);

        for ($i = 0; $i < $productNumber - 1; ++$i) {
            /* @phpstan-ignore-next-line */
            $this->assertEquals($expected, $this->objectListing[$i]->get('name'));
        }
        /* @phpstan-ignore-next-line */
        $this->assertEquals('different name', $this->objectListing[$productNumber]->get('name'));
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

    /**
     * @test
     * @dataProvider dataProviderParams
     *
     * @throws \ReflectionException
     */
    public function testGetParams(
        string $field,
        string $search,
        string $replace,
        string $className,
        string $expectedClassName,
        string $idList,
        array $expectedIds,
        ?string $insensitive,
        bool $expectedIsInsensitive
    ): void {
        /** @phpstan-ignore-next-line */
        $request = $this->createStub(Request::class);
        $request->method('get')
            ->withConsecutive(['field'], ['search'], ['replace'], ['className'], ['idList'], ['insensitive'])
            ->willReturnOnConsecutiveCalls($field, $search, $replace, $className, $idList, $insensitive);

        $controller = new StringReplaceController();
        $reflector = new ReflectionClass($controller);

        $method = $reflector->getMethod('getParams');
        $method->invokeArgs($controller, [$request, true]);
        /* @phpstan-ignore-next-line */
        $this->assertSame($field, $reflector->getProperty('field')->getValue($controller));
        /* @phpstan-ignore-next-line */
        $this->assertSame($search, $reflector->getProperty('search')->getValue($controller));
        /* @phpstan-ignore-next-line */
        $this->assertSame($expectedClassName, $reflector->getProperty('class')->getValue($controller));
        /* @phpstan-ignore-next-line */
        $this->assertSame($expectedIds, $reflector->getProperty('ids')->getValue($controller));
        /* @phpstan-ignore-next-line */
        $this->assertSame($expectedIsInsensitive, $reflector->getProperty('isInsensitive')->getValue($controller));
    }

    public function dataProviderParams(): array
    {
        return [
            ['name', 'lorem', 'ipsum', 'TestObject', "\Pimcore\Model\DataObject\TestObject\Listing", '1,2,3,4', ['1', '2', '3', '4'], null, false],
            ['name', 'lorem', 'ipsum', 'TestObject', "\Pimcore\Model\DataObject\TestObject\Listing", '', [], '1', true],
        ];
    }
}
