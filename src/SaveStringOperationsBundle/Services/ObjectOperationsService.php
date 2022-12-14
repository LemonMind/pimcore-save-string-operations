<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Services;

use Exception;

class ObjectOperationsService
{
    public static function getValueFromField($object, array $field): string|int|float|null
    {
        $language = $field['language'] ?? 'default';
        $languages = \Pimcore\Tool::getValidLanguages();

        if (!in_array($language, [...$languages, 'default'], true)) {
            return null;
        }

        switch ($field['type']) {
            case 'string':
                return $object->get($field['value'], 'default' === $language ? '' : $language);
            case 'input':
                return $field['value'];
            case 'store':
                $keys = explode('-', $field['value'][3]);

                return $object->get($field['value'][2])->getLocalizedKeyValue(intval($keys[0]), intval($keys[1]), $language);
            case 'brick':
                $key = self::getObjectBrickKey($object, $field);

                if ('' === $key) {
                    $brickConfig = json_decode(ltrim($field['value'][0], '?'), true);

                    return $object
                        ->get($brickConfig['fieldname'])
                        ->get($brickConfig['containerKey'])
                        ->get($brickConfig['brickfield'], 'default' === $language ? '' : $language);
                }

                $brick = $object->get($key)->get($field['value'][0]);

                if (is_null($brick)) {
                    return null;
                }

                return $brick->get($field['value'][1]);
            default:
                throw new Exception('Type' . $field['type'] . 'is not supported');
        }
    }

    public static function saveValueToField($object, array $field, mixed $newValue): void
    {
        $language = $field['language'] ?? 'default';
        $languages = \Pimcore\Tool::getValidLanguages();

        if (!in_array($language, [...$languages, 'default'], true)) {
            return;
        }

        switch ($field['type']) {
            case 'string':
            case 'input':
                $object->set($field['value'], $newValue, 'default' === $language ? '' : $language);

                break;
            case 'store':
                $keys = explode('-', $field['value'][3]);
                $object->get($field['value'][2])->setLocalizedKeyValue(intval($keys[0]), intval($keys[1]), $newValue, $language);

                break;
            case 'brick':
                $key = self::getObjectBrickKey($object, $field);

                if ('' === $key) {
                    $brickConfig = json_decode(ltrim($field['value'][0], '?'), true);

                    $object
                        ->get($brickConfig['fieldname'])
                        ->get($brickConfig['containerKey'])
                        ->set($brickConfig['brickfield'], $newValue, 'default' === $language ? '' : $language);

                    return;
                }

                $object->get($key)->get($field['value'][0])->set($field['value'][1], $newValue);

                break;
            default:
                throw new Exception('Type' . $field['type'] . 'is not supported');
        }
    }

    public static function getObjectBrickKey($object, array $field): string
    {
        $objectBrickKey = '';
        $objectClassToArray = (array) $object->get('o_class');

        foreach ($objectClassToArray['fieldDefinitions'] as $key => $value) {
            $valueToArray = (array) $value;

            if ('objectbricks' === $valueToArray['fieldtype']) {
                if (in_array($field['value'][0], $valueToArray['allowedTypes'], true)) {
                    $objectBrickKey = $key;
                }
            }
        }

        return $objectBrickKey;
    }
}
