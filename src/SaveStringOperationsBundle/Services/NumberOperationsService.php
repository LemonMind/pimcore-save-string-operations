<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Services;

use Exception;

class NumberOperationsService
{
    public static function numberOperations($objectListing, array $fields, string $setTo, float $value, string $changeType): bool
    {
        try {
            if ('value' === $setTo) {
                self::numberReplace($objectListing, $fields, $value);
            } else {
                self::percentageReplace($objectListing, $fields, $value, $changeType);
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    private static function numberReplace($objectListing, array $fields, float $number): void
    {
        foreach ($objectListing as $object) {
            $object::setGetInheritedValues(true);

            $fieldNumber = ObjectOperationsService::getValueFromField($object, $fields[0]);

            if (!is_numeric($fieldNumber)) {
                continue;
            }

            ObjectOperationsService::saveValueToField($object, $fields[0], $number);
            $object->save();
        }
    }

    private static function percentageReplace($objectListing, array $fields, float $number, string $changeType): void
    {
        foreach ($objectListing as $object) {
            $object::setGetInheritedValues(true);

            $fieldNumber = ObjectOperationsService::getValueFromField($object, $fields[0]);

            if (!is_numeric($fieldNumber)) {
                continue;
            }

            if ('increase' === $changeType) {
                $fieldNumber += $fieldNumber * $number;
            } else {
                $fieldNumber -= $fieldNumber * $number;
            }

            ObjectOperationsService::saveValueToField($object, $fields[0], $fieldNumber);
            $object->save();
        }
    }
}
