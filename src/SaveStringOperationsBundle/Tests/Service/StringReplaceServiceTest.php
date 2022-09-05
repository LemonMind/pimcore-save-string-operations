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
class StringReplaceServiceTest extends TestCase
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
    public function testStringReplace(string $name, string $search, string $replace, string $expected, bool $isInsensitive, int $productNumber): void
    {
        $service = Mockery::mock('alias:' . ObjectOperationsService::class);

        /* @phpstan-ignore-next-line */
        $service->shouldReceive('getValueFromField')
            ->andReturn($name);

        $objectListing = [];

        for ($i = 0; $i < $productNumber; ++$i) {
            $object = new MockObject($name);
            $objectListing[] = $object;
        }

        $closure = function ($object, $field, $value) {
            $object->setValue($value);

            return true;
        };

        /* @phpstan-ignore-next-line */
        $service->shouldReceive('saveValueToField')
            ->withArgs($closure);

        $reflector = new ReflectionClass('Lemonmind\SaveStringOperationsBundle\Services\StringReplaceService');
        $method = $reflector->getMethod('stringReplace');
        $method->invokeArgs(null, [$objectListing, [['type' => 'string', 'value' => 'name']], $search, $replace, $isInsensitive]);

        for ($i = 0; $i < $productNumber; ++$i) {
            /* @phpstan-ignore-next-line */
            $this->assertEquals($expected, $objectListing[$i]->getValue());
        }
    }

    public function dataProvider(): array
    {
        return [
            ['lorem', 'lorem', 'ipsum', 'ipsum', false, 1],
            ['lorem', 'lorem', 'ipsum', 'ipsum', false, 5],
            ['lorem', 'lorem', 'ipsum', 'ipsum', false, 40],
            ['lorem ipsum', 'lorem', 'ipsum', 'ipsum ipsum', false, 10],
            ['loREm ipsum', 'lorem', 'ipsum', 'ipsum ipsum', true, 10],
            ['loREm ipsum', 'LOREm', 'ipsum', 'ipsum ipsum', true, 10],
        ];
    }
}
