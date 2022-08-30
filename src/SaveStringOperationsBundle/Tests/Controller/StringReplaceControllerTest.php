<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Tests\Controller;

use Lemonmind\SaveStringOperationsBundle\Controller\StringReplaceController;
use Pimcore\Test\KernelTestCase;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;

class StringReplaceControllerTest extends KernelTestCase
{
    /**
     * @test
     * @dataProvider dataProviderParams
     *
     * @throws \ReflectionException
     */
    public function testGetParams(
        string $field,
        array $expectedField,
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
            ->withConsecutive(['field'], ['search'], ['replace'], ['idList'], ['insensitive'], ['className'])
            ->willReturnOnConsecutiveCalls($field, $search, $replace, $idList, $insensitive, $className);

        $controller = new StringReplaceController();
        $reflector = new ReflectionClass($controller);

        $method = $reflector->getMethod('getParams');
        $method->invokeArgs($controller, [$request, true]);
        /* @phpstan-ignore-next-line */
        $this->assertSame($expectedField, $reflector->getProperty('fields')->getValue($controller));
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
            ['name', [['type' => 'string', 'value' => 'name']], 'lorem', 'ipsum', 'TestObject',
                "\Pimcore\Model\DataObject\TestObject\Listing", '1,2,3,4', ['1', '2', '3', '4'], null, false, ],
            ['name', [['type' => 'string', 'value' => 'name']], 'lorem', 'ipsum', 'TestObject',
                "\Pimcore\Model\DataObject\TestObject\Listing", '', [], '1', true, ],
        ];
    }
}
