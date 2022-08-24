<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringReplaceBundle\Tests\Controller;

use Lemonmind\SaveStringReplaceBundle\Controller\StringConcatController;
use Lemonmind\SaveStringReplaceBundle\Tests\TestObject\TestObject;
use Pimcore\Test\KernelTestCase;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;

class StringConcatControllerTest extends KernelTestCase
{
    private array $objectListing;

    /**
     * @test
     * @dataProvider dataProvider
     *
     * @throws \ReflectionException
     */
    public function testStringConcat(
        string $name,
        string $description,
        array $fields,
        string $userInput,
        string $separator,
        string $fieldToSaveConcat,
        string $expected,
        int $productNumber
    ): void {
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
            /* @phpstan-ignore-next-line */
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
