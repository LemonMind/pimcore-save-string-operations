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
                $objectClassToArray = [];
                $objectBrickKey = ' ';

                if ($isObjectBrick) {
                    $objectClassToArray[] = (array) $object->get('o_class');

                    foreach ($objectClassToArray[0]['fieldDefinitions'] as $key => $value) {
                        $valueToArray = (array) $value;

                        if ('objectbricks' === $valueToArray['fieldtype']) {
                            if (in_array($field[0], $valueToArray['allowedTypes'], true)) {
                                $objectBrickKey = $key;
                            }
                        }
                    }

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
