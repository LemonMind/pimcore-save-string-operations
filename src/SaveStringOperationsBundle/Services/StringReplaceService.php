<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Services;

use Exception;

class StringReplaceService
{
    public static function stringReplace($objectListing, array $field, string $search, string $replace, bool $isInsensitive, bool $isObjectBrick): bool
    {
        foreach ($objectListing as $object) {
            try {
                $object::setGetInheritedValues(true);
                $objectBrickKey = ' ';

                if ($isObjectBrick) {
                    $objectBrickKey = ObjectBrickService::objectBrickKey($object, $field);

                    if (null === $object->get($objectBrickKey)->get($field[0])) {
                        continue;
                    }
                    $productField = $object->get($objectBrickKey)->get($field[0])->get($field[1]);
                } else {
                    $productField = $object->get($field[0]);
                }

                if (null !== $productField) {
                    if ($isInsensitive) {
                        $productFieldReplaced = str_ireplace($search, $replace, $productField);
                    } else {
                        $productFieldReplaced = str_replace($search, $replace, $productField);
                    }

                    if (0 != strcasecmp($productFieldReplaced, $productField)) {
                        if ($isObjectBrick) {
                            $object->get($objectBrickKey)->get($field[0])->set($field[1], $productFieldReplaced);
                        } else {
                            $object->set($field[0], $productFieldReplaced);
                        }
                        $object->save();
                    }
                }
            } catch (Exception $e) {
                return false;
            }
        }

        return true;
    }
}
