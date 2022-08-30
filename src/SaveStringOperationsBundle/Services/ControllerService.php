<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Services;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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

    public static function getClass(string $className): string
    {
        if ('' === $className) {
            throw new Exception('Class name is not defined');
        }

        $prefix = "\Pimcore\Model\DataObject";
        $suffix = '\Listing';
        $class = $prefix . "\\$className" . $suffix;

        return $class;
    }

    public static function returnAction(bool $success, string $msg): Response
    {
        return new JsonResponse([
            'success' => $success,
            'msg' => $msg,
        ], $success ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }
}
