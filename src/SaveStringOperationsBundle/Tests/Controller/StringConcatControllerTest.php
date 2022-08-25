<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Tests\Controller;

use Lemonmind\SaveStringOperationsBundle\Controller\StringConcatController;
use Pimcore\Test\KernelTestCase;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;

class StringConcatControllerTest extends KernelTestCase
{
    /**
     * @test
     * @dataProvider dataProviderParams
     *
     * @throws \ReflectionException
     */
    public function testGetParams(
        string $fieldOne,
        string $fieldTwo,
        array $expectedFields,
        string $fieldToSaveConcat,
        string $input,
        string $separator,
        string $idList,
        array $expectedIds,
        string $className,
        string $expectedClassName
    ): void {
        /** @phpstan-ignore-next-line */
        $request = $this->createStub(Request::class);

        if ('input' === $fieldOne) {
            $request->method('get')
                ->withConsecutive(['field_one'], ['field_two'], ['field_save'], ['input_one'], ['separator'], ['idList'], ['className'])
                ->willReturnOnConsecutiveCalls($fieldOne, $fieldTwo, $fieldToSaveConcat, $input, $separator, $idList, $className);
        } elseif ('input' === $fieldTwo) {
            $request->method('get')
                ->withConsecutive(['field_one'], ['field_two'], ['field_save'], ['input_two'], ['separator'], ['idList'], ['className'])
                ->willReturnOnConsecutiveCalls($fieldOne, $fieldTwo, $fieldToSaveConcat, $input, $separator, $idList, $className);
        } else {
            $request->method('get')
                ->withConsecutive(['field_one'], ['field_two'], ['field_save'], ['separator'], ['idList'], ['className'])
                ->willReturnOnConsecutiveCalls($fieldOne, $fieldTwo, $fieldToSaveConcat, $separator, $idList, $className);
        }

        $controller = new StringConcatController();
        $reflector = new ReflectionClass($controller);
        $method = $reflector->getMethod('getParams');

        $method->invokeArgs($controller, [$request, true]);
        /* @phpstan-ignore-next-line */
        $this->assertSame($expectedFields, $reflector->getProperty('fields')->getValue($controller));
        /* @phpstan-ignore-next-line */
        $this->assertSame($fieldToSaveConcat, $reflector->getProperty('fieldToSaveConcat')->getValue($controller));
        /* @phpstan-ignore-next-line */
        $this->assertSame($expectedClassName, $reflector->getProperty('class')->getValue($controller));
        /* @phpstan-ignore-next-line */
        $this->assertSame($expectedIds, $reflector->getProperty('ids')->getValue($controller));

        if ('' !== $input) {
            /* @phpstan-ignore-next-line */
            $this->assertSame($input, $reflector->getProperty('userInput')->getValue($controller));
        }
    }

    public function dataProviderParams(): array
    {
        return [
            ['name', 'description', ['name', 'description'], 'name', '', ' ', '', [], 'class', '\Pimcore\Model\DataObject\class\Listing'],
            ['input', 'description', ['input', 'description'], 'description', 'some text', ',', '1,2,3', ['1', '2', '3'], ' ', '\Pimcore\Model\DataObject\ \Listing'],
            ['name', 'input', ['name', 'input'], 'name', 'some text', ', ', '1,2,3,4,5', ['1', '2', '3', '4', '5'], 'class', '\Pimcore\Model\DataObject\class\Listing'],
        ];
    }
}
