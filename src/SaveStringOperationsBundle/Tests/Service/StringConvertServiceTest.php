<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Tests\Service;

use Lemonmind\SaveStringOperationsBundle\Services\ObjectOperationsService;
use Lemonmind\SaveStringOperationsBundle\Tests\TestObject\MockObject;
use Mockery;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @runInSeparateProcess
 *
 * @preserveGlobalState disabled
 */
class StringConvertServiceTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     *
     * @dataProvider dataProvider
     *
     * @throws \ReflectionException
     */
    public function testStringConvert(string $inital, string $type, array $field, string $expected, int $productNumber): void
    {
        $service = Mockery::mock('alias:' . ObjectOperationsService::class);

        /* @phpstan-ignore-next-line */
        $service->shouldReceive('getValueFromField')
            ->andReturn($inital);

        $objectListing = [];

        for ($i = 0; $i < $productNumber; ++$i) {
            $object = new MockObject($inital);
            $objectListing[] = $object;
        }

        $closure = function ($object, $field, $value) {
            $object->setValue($value);

            return true;
        };

        /* @phpstan-ignore-next-line */
        $service->shouldReceive('saveValueToField')
            ->withArgs($closure);

        $reflector = new ReflectionClass('Lemonmind\SaveStringOperationsBundle\Services\StringConvertService');
        $method = $reflector->getMethod('stringConvert');
        $method->invokeArgs(null, [$objectListing, [$field], $type]);

        for ($i = 0; $i < $productNumber; ++$i) {
            /* @phpstan-ignore-next-line */
            $this->assertEquals($expected, $objectListing[$i]->getValue());
        }
    }

    public function dataProvider(): array
    {
        return [
            ['test', 'upper', ['type' => 'string', 'value' => 'name'], 'TEST', 1],
            ['test', 'lower', ['type' => 'string', 'value' => 'name'], 'test', 1],
            ['test', 'upper', ['type' => 'brick', 'value' => ['TestBrick', 'testField']], 'TEST', 1],
            ['test', 'upper', ['type' => 'store', 'value' => ['', 'classificationstore', 'storeField', '1-1']], 'TEST', 1],
        ];
    }
}
