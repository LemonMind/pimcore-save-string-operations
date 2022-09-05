<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Tests\Controller;

use Lemonmind\SaveStringOperationsBundle\Controller\StringConvertController;
use Pimcore\Test\KernelTestCase;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;

class StringConvertControllerTest extends KernelTestCase
{
    /**
     * @test
     *
     * @dataProvider dataProviderParams
     *
     * @throws \ReflectionException
     */
    public function testGetParams(
        string $language,
        string $field,
        string $type,
        array $expectedField,
        string $className,
        string $expectedClassName,
        string $idList,
        array $expectedIds,
    ): void {
        /** @phpstan-ignore-next-line */
        $request = $this->createStub(Request::class);
        $request->method('get')
            ->withConsecutive(['language'], ['field'], ['type'], ['idList'], ['className'])
            ->willReturnOnConsecutiveCalls($language, $field, $type, $idList, $className);

        $controller = new StringConvertController();
        $reflector = new ReflectionClass($controller);

        $method = $reflector->getMethod('getParams');
        $method->invokeArgs($controller, [$request, true]);
        /* @phpstan-ignore-next-line */
        $this->assertSame($type, $reflector->getProperty('type')->getValue($controller));
        /* @phpstan-ignore-next-line */
        $this->assertSame($expectedField, $reflector->getProperty('fields')->getValue($controller));
        /* @phpstan-ignore-next-line */
        $this->assertSame($expectedClassName, $reflector->getProperty('class')->getValue($controller));
        /* @phpstan-ignore-next-line */
        $this->assertSame($expectedIds, $reflector->getProperty('ids')->getValue($controller));
    }

    public function dataProviderParams(): array
    {
        return [
            [
                'default', 'name', 'lower',
                [['type' => 'string', 'value' => 'name', 'language' => 'default']], 'TestObject',
                "\Pimcore\Model\DataObject\TestObject\Listing", '1,2,3,4', ['1', '2', '3', '4'],
            ],
            [
                'default', 'name', 'upper',
                [['type' => 'string', 'value' => 'name', 'language' => 'default']], 'TestObject',
                "\Pimcore\Model\DataObject\TestObject\Listing", '', [],
            ],
            [
                'default', 'TestBrick~testField', 'lower',
                [['type' => 'brick', 'value' => ['TestBrick', 'testField'], 'language' => 'default']], 'TestObject',
                "\Pimcore\Model\DataObject\TestObject\Listing", '', [],
            ],
            [
                'default', '~classificationstore~storeField~1-1', 'upper',
                [['type' => 'store', 'value' => ['', 'classificationstore', 'storeField', '1-1'], 'language' => 'default']], 'TestObject',
                "\Pimcore\Model\DataObject\TestObject\Listing", '', [],
            ],
        ];
    }
}
