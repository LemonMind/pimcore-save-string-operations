<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Services;

class NumberOperationsService
{
    public static function numberOperations($objectListing, array $field, string $setTo, float $value, string $changeType, bool $isObjectBrick, bool $isClassificationStore): bool
    {
        try {
            if ('value' === $setTo) {
                self::numberReplace($objectListing, $field, $value, $isObjectBrick, $isClassificationStore);
            } else {
                self::percentageReplace($objectListing, $field, $value, $changeType, $isObjectBrick, $isClassificationStore);
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    private static function numberReplace($objectListing, array $field, float $number, bool $isObjectBrick, bool $isClassificationStore): void
    {
        foreach ($objectListing as $object) {
            $object::setGetInheritedValues(true);

            if ($isObjectBrick) {
                $objectBrickKey = ObjectBrickService::objectBrickKey($object, $field);

                if (null === $object->get($objectBrickKey)->get($field[0])) {
                    continue;
                }
                $object->get($objectBrickKey)->get($field[0])->set($field[1], $number);
            } elseif ($isClassificationStore) {
                $items = $object->get($field[2])->getItems();
                $keys = explode('-', $field[3]);
                $items[$keys[0]][$keys[1]]['default'] = $number;
                $object->get($field[2])->setItems($items);
            } else {
                $object->set($field[0], $number);
            }
            $object->save();
        }
    }

    private static function percentageReplace($objectListing, array $field, float $number, string $changeType, bool $isObjectBrick, bool $isClassificationStore): void
    {
        foreach ($objectListing as $object) {
            $object::setGetInheritedValues(true);

            if ($isObjectBrick) {
                $objectBrickKey = ObjectBrickService::objectBrickKey($object, $field);

                if (null === $object->get($objectBrickKey)->get($field[0])) {
                    continue;
                }

                $fieldNumber = $object->get($objectBrickKey)->get($field[0])->get($field[1]);
            } elseif ($isClassificationStore) {
                $items = $object->get($field[2])->getItems();
                $keys = explode('-', $field[3]);
                $fieldNumber = $items[$keys[0]][$keys[1]]['default'];
            } else {
                $fieldNumber = $object->get($field[0]);
            }

            if ('increase' === $changeType) {
                $fieldNumber += $fieldNumber * $number;
            } else {
                $fieldNumber -= $fieldNumber * $number;
            }

            if ($isObjectBrick) {
                if (isset($objectBrickKey)) {
                    $object->get($objectBrickKey)->get($field[0])->set($field[1], $fieldNumber);
                }
            } elseif ($isClassificationStore) {
                if (isset($keys)) {
                    $items[$keys[0]][$keys[1]]['default'] = $fieldNumber;
                    $object->get($field[2])->setItems($items);
                }
            } else {
                $object->set($field[0], $fieldNumber);
            }
            $object->save();
        }
    }
}
