<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Tests\Controller;

use Lemonmind\SaveStringOperationsBundle\Controller\NumberOperationsController;
use Pimcore\Test\KernelTestCase;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;

class NumberOperationsControllerTest extends KernelTestCase
{
    /**
     * @test
     * @dataProvider dataProvider
     *
     * @throws \ReflectionException
     */
    public function testGetParams(
        string $field,
        array $expectedField,
        string $setTo,
        string $changeType,
        float $value,
        string $idList,
        array $expectedIds,
        string $className,
        string $expectedClassName
    ): void {
        /** @phpstan-ignore-next-line */
        $request = $this->createStub(Request::class);

        if ('value' === $setTo) {
            $request->method('get')
                ->withConsecutive(['field'], ['set_to'], ['value'], ['className'], ['idList'])
                ->willReturnOnConsecutiveCalls($field, $setTo, $value, $className, $idList);
        } else {
            $request->method('get')
                ->withConsecutive(['field'], ['set_to'], ['value'], ['className'], ['idList'], ['change_type'])
                ->willReturnOnConsecutiveCalls($field, $setTo, $value, $className, $idList, $changeType);
        }

        $controller = new NumberOperationsController();
        $reflector = new ReflectionClass($controller);
        $method = $reflector->getMethod('getParams');

        $method->invokeArgs($controller, [$request, true]);
        /* @phpstan-ignore-next-line */
        $this->assertSame($expectedField, $reflector->getProperty('field')->getValue($controller));
        /* @phpstan-ignore-next-line */
        $this->assertSame($setTo, $reflector->getProperty('setTo')->getValue($controller));

        if ('' !== $changeType) {
            /* @phpstan-ignore-next-line */
            $this->assertSame($changeType, $reflector->getProperty('changeType')->getValue($controller));
            /* @phpstan-ignore-next-line */
            $this->assertSame($value / 100, $reflector->getProperty('value')->getValue($controller));
        } else {
            /* @phpstan-ignore-next-line */
            $this->assertSame($value, $reflector->getProperty('value')->getValue($controller));
        }
        /* @phpstan-ignore-next-line */
        $this->assertSame($expectedIds, $reflector->getProperty('ids')->getValue($controller));
        /* @phpstan-ignore-next-line */
        $this->assertSame($expectedClassName, $reflector->getProperty('class')->getValue($controller));
    }

    public function dataProvider(): array
    {
        return [
            ['price', ['price'], 'value', '', 100, '', [], 'class', '\Pimcore\Model\DataObject\class\Listing'],
            ['price', ['price'], 'percentage', 'decrease', 50, '1,2,3', ['1', '2', '3'], 'sometext', '\Pimcore\Model\DataObject\sometext\Listing'],
            ['price', ['price'], 'percentage', 'increase', 0.12, '1,2,3', ['1', '2', '3'], 'sometext', '\Pimcore\Model\DataObject\sometext\Listing'],
        ];
    }
}
