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
class StringConcatServiceTest extends TestCase
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
    public function testStringConcat(
        string $name,
        string $description,
        array $fields,
        string $separator,
        string $expected,
        int $productNumber
    ): void {
        $service = Mockery::mock('alias:' . ObjectOperationsService::class);

        $objectListing = [];

        for ($i = 0; $i <= $productNumber; ++$i) {
            $object = new MockObject($name);
            $objectListing[] = $object;

            /* @phpstan-ignore-next-line */
            $service->shouldReceive('getValueFromField')
                ->with($object, $fields[0])
                ->andReturn($name);

            /* @phpstan-ignore-next-line */
            $service->shouldReceive('getValueFromField')
                ->with($object, $fields[1])
                ->andReturn($description);
        }

        $closure = function ($object, $field, $value) {
            $object->setValue($value);

            return true;
        };

        /* @phpstan-ignore-next-line */
        $service->shouldReceive('saveValueToField')
            ->withArgs($closure);

        $reflector = new ReflectionClass('Lemonmind\SaveStringOperationsBundle\Services\StringConcatService');
        $method = $reflector->getMethod('stringConcat');

        $method->invokeArgs(null, [$objectListing, $fields, $separator]);

        for ($i = 0; $i < $productNumber; ++$i) {
            /* @phpstan-ignore-next-line */
            $this->assertEquals($expected, $objectListing[$i]->getValue());
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
            ['lorem', 'ipsum', [
                ['type' => 'brick', 'value' => 'name'],
                ['type' => 'store', 'value' => 'description'],
                ['type' => 'string', 'value' => 'name'],
            ], ';', 'lorem;ipsum', 10],
        ];
    }
}
