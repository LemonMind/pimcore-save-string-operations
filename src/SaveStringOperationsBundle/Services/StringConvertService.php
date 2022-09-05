<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Services;

use Exception;

class StringConvertService
{
    public static function stringConvert($objectListing, array $field, string $type): bool
    {
        foreach ($objectListing as $object) {
            try {
                $object::setGetInheritedValues(true);
                $productField = ObjectOperationsService::getValueFromField($object, $field[0]);

                if (!is_string($productField)) {
                    continue;
                }

                $productFieldConverted = strip_tags($productField);

                if ('upper' === $type) {
                    $productFieldConverted = strtoupper($productField);
                }

                if ('lower' === $type) {
                    $productFieldConverted = strtolower($productField);
                }

                ObjectOperationsService::saveValueToField($object, $field[0], $productFieldConverted);
                $object->save();
            } catch (Exception $e) {
                return false;
            }
        }

        return true;
    }
}
