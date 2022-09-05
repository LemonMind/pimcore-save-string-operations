<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Tests\Service;

use Lemonmind\SaveStringOperationsBundle\Services\ObjectOperationsService;
// use ReflectionClass;

use Lemonmind\SaveStringOperationsBundle\Tests\TestObject\MockObject;
use Lemonmind\SaveStringOperationsBundle\Tests\TestObject\TestObject;
use Mockery;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @runInSeparateProcess
 *
 * @preserveGlobalState disabled
 */
class NumberOperationsServiceTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     *
     * @dataProvider dataProviderNumberReplace
     *
     * @throws \ReflectionException
     */
    public function testNumberReplace(float $initial, array $field, float $value, float $expected, int $productNumber): void
    {
        $service = Mockery::mock('alias:' . ObjectOperationsService::class);

        $objectListing = [];

        for ($i = 0; $i <= $productNumber; ++$i) {
            $object = new MockObject($initial);
            $objectListing[] = $object;

            /* @phpstan-ignore-next-line */
            $service->shouldReceive('getValueFromField')
                ->with($object, $field)
                ->andReturn($value);
        }

        $closure = function ($object, $field, $value) {
            $object->setValue($value);

            return true;
        };

        /* @phpstan-ignore-next-line */
        $service->shouldReceive('saveValueToField')
            ->withArgs($closure);

        $reflector = new ReflectionClass('Lemonmind\SaveStringOperationsBundle\Services\NumberOperationsService');
        $method = $reflector->getMethod('numberReplace');
        $method->invokeArgs(null, [$objectListing, [$field], $value]);

        for ($i = 0; $i < $productNumber; ++$i) {
            /* @phpstan-ignore-next-line */
            $this->assertEquals($expected, $objectListing[$i]->getValue());
        }
    }

    public function dataProviderNumberReplace(): array
    {
        return [
            [0, ['type' => 'string', 'value' => 'name'], 100, 100, 1],
            [0, ['type' => 'string', 'value' => 'name'], 100, 100, 10],
            [0, ['type' => 'brick', 'value' => 'name'], 50, 50, 1],
            [0, ['type' => 'store', 'value' => 'name'], 50, 50, 1],
        ];
    }

    /**
     * @test
     *
     * @dataProvider dataProviderPercentageReplace
     *
     * @throws \ReflectionException
     */
    public function testPercentageReplace(float $initial, array $field, float $value, string $changeType, float $expected, int $productNumber): void
    {
        $service = Mockery::mock('alias:' . ObjectOperationsService::class);

        $objectListing = [];

        for ($i = 0; $i <= $productNumber; ++$i) {
            $object = new MockObject($initial);
            $objectListing[] = $object;

            /* @phpstan-ignore-next-line */
            $service->shouldReceive('getValueFromField')
                ->with($object, $field)
                ->andReturn($initial);
        }

        $closure = function ($object, $field, $value) {
            $object->setValue($value);

            return true;
        };

        /* @phpstan-ignore-next-line */
        $service->shouldReceive('saveValueToField')
            ->withArgs($closure);

        $reflector = new ReflectionClass('Lemonmind\SaveStringOperationsBundle\Services\NumberOperationsService');
        $method = $reflector->getMethod('percentageReplace');
        $method->invokeArgs(null, [$objectListing, [$field], $value, $changeType]);

        for ($i = 0; $i < $productNumber; ++$i) {
            /* @phpstan-ignore-next-line */
            $this->assertEqualsWithDelta($expected, $objectListing[$i]->getValue(), 0.0001);
        }
    }

    public function dataProviderPercentageReplace(): array
    {
        return [
            [100, ['type' => 'string', 'value' => 'name'], 0.1, 'increase', 110, 1],
            [100, ['type' => 'string', 'value' => 'name'], 0.1, 'increase', 110, 10],
            [100, ['type' => 'string', 'value' => 'name'], 0.1, 'decrease', 90, 1],
            [100, ['type' => 'string', 'value' => 'name'], 0.1, 'decrease', 90, 10],
        ];
    }
}
