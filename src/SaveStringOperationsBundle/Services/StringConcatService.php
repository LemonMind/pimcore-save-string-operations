<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Services;

use Exception;

class StringConcatService
{
    public static function stringConcat($objectListing, array $fields, string $separator): bool
    {
        foreach ($objectListing as $object) {
            try {
                $object::setGetInheritedValues(true);
                $field_one = ObjectOperationsService::getValueFromField($object, $fields[0]);
                $field_two = ObjectOperationsService::getValueFromField($object, $fields[1]);

                if (is_null($field_one) || is_null($field_two)) {
                    continue;
                }

                $field_one = is_string($field_one) ? strip_tags($field_one) : $field_one;
                $field_two = is_string($field_two) ? strip_tags($field_two) : $field_two;

                ObjectOperationsService::saveValueToField($object, $fields[2], $field_one . $separator . $field_two);
                $object->save();
            } catch (Exception $e) {
                return false;
            }
        }

        return true;
    }
}
