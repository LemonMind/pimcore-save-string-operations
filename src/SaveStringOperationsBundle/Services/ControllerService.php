<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Services;

class ControllerService
{
    public static function getFields(array $fieldsArray): array
    {
        $fields = [];

        foreach ($fieldsArray as $value) {
            if (str_contains($value, '~')) {
                $value = explode('~', $value);

                if ('classificationstore' === $value[1]) {
                    $fields[] = ['type' => 'store', 'value' => $value];
                } else {
                    $fields[] = ['type' => 'brick', 'value' => $value];
                }

                continue;
            }

            if (is_numeric($value)) {
                $fields[] = ['type' => 'number', 'value' => $value];

                continue;
            }

            $fields[] = ['type' => 'string', 'value' => $value];
        }

        return $fields;
    }
}
