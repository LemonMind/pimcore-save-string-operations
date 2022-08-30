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
        string $fieldSave,
        string $inputOne,
        string $inputTwo,
        array $expectedFields,
        string $separator,
        string $className,
        string $expectedClassName,
        string $ids,
        array $expectedIds
    ): void {
        /** @phpstan-ignore-next-line */
        $request = $this->createStub(Request::class);

        $request->method('get')
            ->withConsecutive(['field_one'], ['field_two'], ['field_save'], ['input_one'], ['input_two'], ['separator'], ['idList'], ['className'])
            ->willReturnOnConsecutiveCalls($fieldOne, $fieldTwo, $fieldSave, $inputOne, $inputTwo, $separator, $ids, $className);

        $controller = new StringConcatController();
        $reflector = new ReflectionClass($controller);
        $method = $reflector->getMethod('getParams');

        $method->invokeArgs($controller, [$request, true]);
        /* @phpstan-ignore-next-line */
        $this->assertSame($expectedFields, $reflector->getProperty('fields')->getValue($controller));
        /* @phpstan-ignore-next-line */
        $this->assertSame($separator, $reflector->getProperty('separator')->getValue($controller));
        /* @phpstan-ignore-next-line */
        $this->assertSame($expectedClassName, $reflector->getProperty('class')->getValue($controller));
        /* @phpstan-ignore-next-line */
        $this->assertSame($expectedIds, $reflector->getProperty('ids')->getValue($controller));
    }

    public function dataProviderParams(): array
    {
        // ['field_one', 'field_two', 'field_save', 'input_one', 'input_two', expected_fields, separator, className, expetedClass, ids, expextedIds]
        return [
            ['name', 'description', 'name', '', '', [
                ['type' => 'string', 'value' => 'name'],
                ['type' => 'string', 'value' => 'description'],
                ['type' => 'string', 'value' => 'name'],
            ], '', 'TestClass', '\Pimcore\Model\DataObject\TestClass\Listing', '', []],
            ['input', 'description', 'name', 'test', '', [
                ['type' => 'input', 'value' => 'test'],
                ['type' => 'string', 'value' => 'description'],
                ['type' => 'string', 'value' => 'name'],
            ], '', 'TestClass', '\Pimcore\Model\DataObject\TestClass\Listing', '', []],
            ['name', 'input', 'name', '', 'test', [
                ['type' => 'string', 'value' => 'name'],
                ['type' => 'input', 'value' => 'test'],
                ['type' => 'string', 'value' => 'name'],
            ], '', 'TestClass', '\Pimcore\Model\DataObject\TestClass\Listing', '', []],
            ['input', 'input', 'name', 'test1', 'test2', [
                ['type' => 'input', 'value' => 'test1'],
                ['type' => 'input', 'value' => 'test2'],
                ['type' => 'string', 'value' => 'name'],
            ], '', 'TestClass', '\Pimcore\Model\DataObject\TestClass\Listing', '', []],
            ['TestBrick~testField', 'name', 'name', '', '', [
                ['type' => 'brick', 'value' => ['TestBrick', 'testField']],
                ['type' => 'string', 'value' => 'name'],
                ['type' => 'string', 'value' => 'name'],
            ], '', 'TestClass', '\Pimcore\Model\DataObject\TestClass\Listing', '', []],
            ['~classificationstore~storeField~1-1', 'name', 'name', '', '', [
                ['type' => 'store', 'value' => ['', 'classificationstore', 'storeField', '1-1']],
                ['type' => 'string', 'value' => 'name'],
                ['type' => 'string', 'value' => 'name'],
            ], '', 'TestClass', '\Pimcore\Model\DataObject\TestClass\Listing', '', []],
        ];
    }
}
