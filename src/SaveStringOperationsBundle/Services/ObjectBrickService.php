<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Services;

use Pimcore\Model\DataObject\AbstractObject;

class ObjectBrickService
{
    public static function objectBrickKey(AbstractObject $object, array $field): string
    {
        $objectBrickKey = ' ';
        $objectClassToArray[] = (array) $object->get('o_class');

        foreach ($objectClassToArray[0]['fieldDefinitions'] as $key => $value) {
            $valueToArray = (array) $value;

            if ('objectbricks' === $valueToArray['fieldtype']) {
                if (in_array($field[0], $valueToArray['allowedTypes'], true)) {
                    $objectBrickKey = $key;
                }
            }
        }

        return $objectBrickKey;
    }
}
