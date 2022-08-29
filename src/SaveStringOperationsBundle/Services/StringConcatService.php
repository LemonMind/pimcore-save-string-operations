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
                $field_one = self::getValueFromField($object, $fields[0]);
                $field_two = self::getValueFromField($object, $fields[1]);

                self::saveValueToField($object, $fields[2], $field_one . $separator . $field_two);
                $object->save();
            } catch (Exception $e) {
                return false;
            }
        }

        return true;
    }

    private static function getObjectBrickKey($object, array $field): string
    {
        $objectClassToArray = (array) $object->get('o_class');

        foreach ($objectClassToArray['fieldDefinitions'] as $key => $value) {
            $valueToArray = (array) $value;

            if ('objectbricks' === $valueToArray['fieldtype']) {
                if (in_array($field['value'][0], $valueToArray['allowedTypes'], true)) {
                    return $key;
                }
            }
        }
    }

    private static function getValueFromField($object, array $field): string
    {
        switch ($field['type']) {
            case 'string':
                return $object->get($field['value']);

                break;
            case 'input':
                return $field['value'];

                break;
            case 'store':
                $keys = explode('-', $field['value'][3]);

                return $object->get($field['value'][2])->getLocalizedKeyValue(intval($keys[0]), intval($keys[1]));

                break;
            case 'brick':
                $key = self::getObjectBrickKey($object, $field);

                return $object->get($key)->get($field['value'][0])->get($field['value'][1]);

                break;
            default:
                throw new Exception('Type' . $field['type'] . 'is not supported');
        }
    }

    private static function saveValueToField($object, array $field, string $newValue): void
    {
        switch ($field['type']) {
            case 'string':
            case 'input':
                $object->set($field['value'], $newValue);

                break;
            case 'store':
                $keys = explode('-', $field['value'][3]);
                $object->get($field['value'][2])->setLocalizedKeyValue(intval($keys[0]), intval($keys[1]), $newValue);

                break;
            case 'brick':
                $key = self::getObjectBrickKey($object, $field);
                $object->get($key)->get($field['value'][0])->get($field['value'][1]);

                break;
            default:
                throw new Exception('Type' . $field['type'] . 'is not supported');
        }
    }
}
