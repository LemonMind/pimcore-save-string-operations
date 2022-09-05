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
     *
     * @dataProvider dataProviderParams
     *
     * @throws \ReflectionException
     */
    public function testGetParams(
        ?string $language,
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
            ->withConsecutive(['language'], ['field_one'], ['field_two'], ['field_save'], ['input_one'], ['input_two'], ['separator'], ['idList'], ['className'])
            ->willReturnOnConsecutiveCalls($language, $fieldOne, $fieldTwo, $fieldSave, $inputOne, $inputTwo, $separator, $ids, $className);

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
            ['default', 'name', 'description', 'name', '', '', [
                ['type' => 'string', 'value' => 'name', 'language' => 'default'],
                ['type' => 'string', 'value' => 'description', 'language' => 'default'],
                ['type' => 'string', 'value' => 'name', 'language' => 'default'],
            ], '', 'TestClass', '\Pimcore\Model\DataObject\TestClass\Listing', '', []],
            ['default', 'input', 'description', 'name', 'test', '', [
                ['type' => 'input', 'value' => 'test', 'language' => 'default'],
                ['type' => 'string', 'value' => 'description', 'language' => 'default'],
                ['type' => 'string', 'value' => 'name', 'language' => 'default'],
            ], '', 'TestClass', '\Pimcore\Model\DataObject\TestClass\Listing', '', []],
            ['default', 'name', 'input', 'name', '', 'test', [
                ['type' => 'string', 'value' => 'name', 'language' => 'default'],
                ['type' => 'input', 'value' => 'test', 'language' => 'default'],
                ['type' => 'string', 'value' => 'name', 'language' => 'default'],
            ], '', 'TestClass', '\Pimcore\Model\DataObject\TestClass\Listing', '', []],
            ['default', 'input', 'input', 'name', 'test1', 'test2', [
                ['type' => 'input', 'value' => 'test1', 'language' => 'default'],
                ['type' => 'input', 'value' => 'test2', 'language' => 'default'],
                ['type' => 'string', 'value' => 'name', 'language' => 'default'],
            ], '', 'TestClass', '\Pimcore\Model\DataObject\TestClass\Listing', '', []],
            ['default', 'TestBrick~testField', 'name', 'name', '', '', [
                ['type' => 'brick', 'value' => ['TestBrick', 'testField'], 'language' => 'default'],
                ['type' => 'string', 'value' => 'name', 'language' => 'default'],
                ['type' => 'string', 'value' => 'name', 'language' => 'default'],
            ], '', 'TestClass', '\Pimcore\Model\DataObject\TestClass\Listing', '', []],
            ['default', '~classificationstore~storeField~1-1', 'name', 'name', '', '', [
                ['type' => 'store', 'value' => ['', 'classificationstore', 'storeField', '1-1'], 'language' => 'default'],
                ['type' => 'string', 'value' => 'name', 'language' => 'default'],
                ['type' => 'string', 'value' => 'name', 'language' => 'default'],
            ], '', 'TestClass', '\Pimcore\Model\DataObject\TestClass\Listing', '', []],
        ];
    }
}
