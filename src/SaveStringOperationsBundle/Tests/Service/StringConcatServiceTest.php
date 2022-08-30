<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Tests\Service;

use Lemonmind\SaveStringOperationsBundle\Services\StringConcatService;
use Lemonmind\SaveStringOperationsBundle\Tests\TestObject\TestObject;
use Pimcore\Test\KernelTestCase;
use ReflectionClass;

class StringConcatServiceTest extends KernelTestCase
{
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
        string $separator,
        string $expected,
        int $productNumber
    ): void {
        $objectListing = [];

        for ($i = 0; $i < $productNumber; ++$i) {
            $objectListing[] = new TestObject($name, $description, 0);
        }

        $reflector = new ReflectionClass('Lemonmind\SaveStringOperationsBundle\Services\StringConcatService');
        $method = $reflector->getMethod('stringConcat');


        $method->invokeArgs(null, [$objectListing, $fields, $separator]);

        for ($i = 0; $i < $productNumber; ++$i) {
            /* @phpstan-ignore-next-line */
            $this->assertEquals($expected, $objectListing[$i]->get($fields[2]['value']));
        }
    }

    public function dataProvider(): array
    {
        //  $name, $description $fields, $separator, $expected, $productNumber
        return [
            ['', '', [
                ['type' => 'string', 'value' => 'name'],
                ['type' => 'string', 'value' => 'description'],
                ['type' => 'string', 'value' => 'name'],
            ], '', '', 10],
            ['lorem', '', [
                ['type' => 'string', 'value' => 'name'],
                ['type' => 'string', 'value' => 'description'],
                ['type' => 'string', 'value' => 'name'],
            ], '', 'lorem', 10],
            ['', 'ipsum', [
                ['type' => 'string', 'value' => 'name'],
                ['type' => 'string', 'value' => 'description'],
                ['type' => 'string', 'value' => 'name'],
            ], '', 'ipsum', 10],
            ['lorem', 'ipsum', [
                ['type' => 'string', 'value' => 'name'],
                ['type' => 'string', 'value' => 'description'],
                ['type' => 'string', 'value' => 'name'],
            ], '', 'loremipsum', 10],
            ['lorem', 'ipsum', [
                ['type' => 'string', 'value' => 'name'],
                ['type' => 'string', 'value' => 'description'],
                ['type' => 'string', 'value' => 'name'],
            ], ';', 'lorem;ipsum', 10],
        ];
    }
}
