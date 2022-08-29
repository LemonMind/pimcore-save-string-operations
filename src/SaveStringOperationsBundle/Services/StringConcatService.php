<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Services;

use Exception;

class StringConcatService
{
    public static function stringConcat($objectListing, array $fields, string $userInput, string|array $fieldToSaveConcat, string $separator, bool $isObjectBrick, bool $isClassificationStore): bool
    {
        foreach ($objectListing as $object) {
            try {
                $object::setGetInheritedValues(true);
                $fieldsValues = [0 => '', 1 => ''];
                $objectBrickKey = [];

                if ($isObjectBrick) {
                    $objectBrickKey = self::objectBrickKey((array) $object->get('o_class'), $fields, $fieldToSaveConcat);

                    if ('' !== $objectBrickKey[0]) {
                        if (null === $object->get($objectBrickKey[0])->get($fields[0][0])) {
                            continue;
                        }
                        $fieldsValues[0] = $object->get($objectBrickKey[0])->get($fields[0][0])->get($fields[0][1]);
                    }

                    if ('' !== $objectBrickKey[1]) {
                        if (null === $object->get($objectBrickKey[1])->get($fields[1][0])) {
                            continue;
                        }
                        $fieldsValues[1] = $object->get($objectBrickKey[1])->get($fields[1][0])->get($fields[1][1]);
                    }
                } elseif ($isClassificationStore) {
                    if ('classificationstore' === $fields[0][1]) {
                        $firstFieldItems = $object->get($fields[0][2])->getItems();
                        $firstFieldKeys = explode('-', $fields[0][3]);
                        $fieldsValues[0] = $firstFieldItems[$firstFieldKeys[0]][$firstFieldKeys[1]]['default'];
                    }

                    if ('classificationstore' === $fields[1][1]) {
                        $secondFieldItems = $object->get($fields[1][2])->getItems();
                        $secondFieldKeys = explode('-', $fields[1][3]);
                        $fieldsValues[1] = $secondFieldItems[$secondFieldKeys[0]][$secondFieldKeys[1]]['default'];
                    }

                    if ('classificationstore' === $fieldToSaveConcat[1]) {
                        $fieldToSaveItems = $object->get($fieldToSaveConcat[2])->getItems();
                        $fieldToSaveKeys = explode('-', $fieldToSaveConcat[3]);
                    }
                }

                if ('' !== $userInput) {
                    if ('input' === $fields[0]) {
                        $fieldsValues[0] = $userInput;
                    } else {
                        $fieldsValues[1] = $userInput;
                    }
                }

                if ('' === $fieldsValues[0]) {
                    $fieldsValues[0] = $object->get($fields[0]);
                }

                if ('' === $fieldsValues[1]) {
                    $fieldsValues[1] = $object->get($fields[1]);
                }

                $field = strip_tags($fieldsValues[0]) . $separator . strip_tags($fieldsValues[1]);

                if (is_array($fieldToSaveConcat)) {
                    if ('classificationstore' === $fieldToSaveConcat[1]) {
                        if (isset($fieldToSaveItems) && isset($fieldToSaveKeys)) {
                            $fieldToSaveItems[$fieldToSaveKeys[0]][$fieldToSaveKeys[1]]['default'] = $field;
                            $object->get($fieldToSaveConcat[2])->setItems($fieldToSaveItems);
                        }
                    } else {
                        $object->get($objectBrickKey['save'])->get($fieldToSaveConcat[0])->set($fieldToSaveConcat[1], $field);
                    }
                } else {
                    $object->set($fieldToSaveConcat, $field);
                }
                $object->save();
            } catch (Exception $e) {
                return false;
            }
        }

        return true;
    }

    private static function objectBrickKey(array $objectClassToArray, array $fields, string|array $fieldToSaveConcat): array
    {
        $objectBrickKey = [0 => '', 1 => '', 'save' => ''];

        foreach ($objectClassToArray['fieldDefinitions'] as $key => $value) {
            $valueToArray = (array) $value;

            if ('objectbricks' === $valueToArray['fieldtype']) {
                if (in_array($fields[0][0], $valueToArray['allowedTypes'], true)) {
                    $objectBrickKey[0] = $key;
                }

                if (in_array($fields[1][0], $valueToArray['allowedTypes'], true)) {
                    $objectBrickKey[1] = $key;
                }

                if (is_array($fieldToSaveConcat)) {
                    if (in_array($fieldToSaveConcat[0], $valueToArray['allowedTypes'], true)) {
                        $objectBrickKey['save'] = $key;
                    }
                }
            }
        }

        return $objectBrickKey;
    }
}
