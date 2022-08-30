<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Services;

use Exception;

class StringReplaceService
{
    public static function stringReplace($objectListing, array $field, string $search, string $replace, bool $isInsensitive): bool
    {
        foreach ($objectListing as $object) {
            try {
                $object::setGetInheritedValues(true);
                $productField = ObjectOperationsService::getValueFromField($object, $field[0]);

                if (!is_string($productField)) {
                    continue;
                }

                if ($isInsensitive) {
                    $productFieldReplaced = str_ireplace($search, $replace, $productField);
                } else {
                    $productFieldReplaced = str_replace($search, $replace, $productField);
                }

                if (0 != strcasecmp($productFieldReplaced, $productField)) {
                    ObjectOperationsService::saveValueToField($object, $field[0], $productFieldReplaced);
                    $object->save();
                }
            } catch (Exception $e) {
                return false;
            }
        }

        return true;
    }
}
