<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Tests\Service;

use Lemonmind\SaveStringOperationsBundle\Tests\TestObject\TestObject;
use Pimcore\Test\KernelTestCase;
use ReflectionClass;

class StringReplaceServiceTest extends KernelTestCase
{
    /**
     * @test
     * @dataProvider dataProvider
     *
     * @throws \ReflectionException
     */
    public function testStringReplace(string $name, string $search, string $replace, string $expected, bool $isInsensitive, int $productNumber): void
    {
        for ($i = 0; $i < $productNumber; ++$i) {
            $objectListing[] = new TestObject($name, '', 0);
        }
        $objectListing[] = new TestObject('different name', '', 0);

        $reflector = new ReflectionClass('Lemonmind\SaveStringOperationsBundle\Services\StringReplaceService');
        $method = $reflector->getMethod('stringReplace');
        $method->invokeArgs(null, [$objectListing, ['name'], $search, $replace, $isInsensitive, false]);

        for ($i = 0; $i < $productNumber - 1; ++$i) {
            /* @phpstan-ignore-next-line */
            $this->assertEquals($expected, $objectListing[$i]->get('name'));
        }
        /* @phpstan-ignore-next-line */
        $this->assertEquals('different name', $objectListing[$productNumber]->get('name'));
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
