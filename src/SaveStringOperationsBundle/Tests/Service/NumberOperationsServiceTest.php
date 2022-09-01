<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Tests\Service;

use Lemonmind\SaveStringOperationsBundle\Tests\TestObject\TestObject;
use Pimcore\Test\KernelTestCase;
use ReflectionClass;

class NumberOperationsServiceTest extends KernelTestCase
{
    /**
     * @test
     * @dataProvider dataProviderNumberOperations
     *
     * @throws \ReflectionException
     */
    public function testNumberOperations(string $setTo, float $value, float $expected): void
    {
        $objectListing = [];
        $objectListing[] = new TestObject('name', '', 100);

        $reflector = new ReflectionClass('Lemonmind\SaveStringOperationsBundle\Services\NumberOperationsService');
        $method = $reflector->getMethod('numberOperations');
        $method->invokeArgs(null, [$objectListing, [['type' => 'string', 'value' => 'price']], $setTo, $value, 'increase']);

        /* @phpstan-ignore-next-line */
        $this->assertEquals($expected, $objectListing[0]->get('price'));
    }

    public function dataProviderNumberOperations(): array
    {
        return [
            ['value', 100, 100],
            ['percentage', 0.1, 110],
        ];
    }

    /**
     * @test
     * @dataProvider dataProviderNumberReplace
     *
     * @throws \ReflectionException
     */
    public function testNumberReplace(float $value, int $productNumber): void
    {
        $objectListing = [];

        for ($i = 0; $i < $productNumber; ++$i) {
            $objectListing[] = new TestObject('name', '', 0);
        }
        $reflector = new ReflectionClass('Lemonmind\SaveStringOperationsBundle\Services\NumberOperationsService');
        $method = $reflector->getMethod('numberReplace');
        $method->invokeArgs(null, [$objectListing, [['type' => 'string', 'value' => 'price']], $value]);

        for ($i = 0; $i < $productNumber; ++$i) {
            /* @phpstan-ignore-next-line */
            $this->assertEquals($value, $objectListing[$i]->get('price'));
        }
    }

    public function dataProviderNumberReplace(): array
    {
        return [
            [100, 1],
            [1150, 10],
            [10.12, 10],
        ];
    }

    /**
     * @test
     * @dataProvider dataProviderPercentageReplace
     *
     * @throws \ReflectionException
     */
    public function testPercentageReplace(float $price, float $value, float $expected, string $changeType, int $productNumber): void
    {
        $objectListing = [];

        for ($i = 0; $i < $productNumber; ++$i) {
            $objectListing[] = new TestObject('name', '', $price);
        }
        $reflector = new ReflectionClass('Lemonmind\SaveStringOperationsBundle\Services\NumberOperationsService');
        $method = $reflector->getMethod('percentageReplace');
        $method->invokeArgs(null, [$objectListing, [['type' => 'string', 'value' => 'price']], $value, $changeType]);

        for ($i = 0; $i < $productNumber; ++$i) {
            /* @phpstan-ignore-next-line */
            $this->assertEquals($expected, $objectListing[$i]->get('price'));
        }
    }

    public function dataProviderPercentageReplace(): array
    {
        return [
            [100, 0.1, 110, 'increase', 1],
            [100, 0.1, 90, 'decrease', 1],
            [200, 0.5, 300, 'increase', 10],
            [200, 0.5, 100, 'decrease', 10],
        ];
    }
}
