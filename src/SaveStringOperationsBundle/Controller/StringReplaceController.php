<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Controller;

use Exception;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/string_replace")
 */
class StringReplaceController extends AdminController
{
    private array $field;
    private string $search;
    private string $replace;
    private bool $isInsensitive;
    private bool $isObjectBrick = false;
    private string $class;
    private array $ids;

    /**
     * @Route("/selected")
     */
    public function selectedAction(Request $request): Response
    {
        $this->getParams($request);
        $objectListing = new $this->class();
        $objectListing->addConditionParam('o_id IN (?)', [$this->ids]);
        $this->stringReplace($objectListing);

        return $this->returnAction(true, '');
    }

    /**
     * @Route("/all")
     */
    public function allAction(Request $request): Response
    {
        $this->getParams($request);
        $objectListing = new $this->class();
        $this->stringReplace($objectListing);

        return $this->returnAction(true, '');
    }

    /**
     * @throws Exception
     */
    private function getParams(Request $request, bool $test = false): void
    {
        $this->field[] = $request->get('field');
        $this->search = $request->get('search');
        $this->replace = $request->get('replace');
        $className = $request->get('className');

        if (str_contains($this->field[0], '~')) {
            $this->field = explode('~', $this->field[0]);
            $this->isObjectBrick = true;
        }

        if ('' === $className) {
            throw new Exception('Class name is not defined');
        }

        $this->ids = array_filter(explode(',', trim($request->get('idList'))));
        $this->isInsensitive = null !== $request->get('insensitive');

        $prefix = "\Pimcore\Model\DataObject";
        $suffix = '\Listing';
        $this->class = $prefix . "\\$className" . $suffix;

        if (!class_exists($this->class)) {
            if ($test) {
                return;
            }
            $this->returnAction(false, 'Class does not exist');
        }
    }

    private function stringReplace($objectListing): void
    {
        foreach ($objectListing as $object) {
            try {
                $objectClassToArray = [];
                $objectBrickKey = ' ';

                if ($this->isObjectBrick) {
                    $objectClassToArray[] = (array) $object->get('o_class');

                    foreach ($objectClassToArray[0]['fieldDefinitions'] as $key => $value) {
                        $valueToArray = (array) $value;

                        if ('objectbricks' === $valueToArray['fieldtype']) {
                            if (in_array($this->field[0], $valueToArray['allowedTypes'], true)) {
                                $objectBrickKey = $key;
                            }
                        }
                    }

                    if (null === $object->get($objectBrickKey)->get($this->field[0])) {
                        continue;
                    }
                    $productField = $object->get($objectBrickKey)->get($this->field[0])->get($this->field[1]);
                } else {
                    $productField = $object->get($this->field[0]);
                }

                if (null !== $productField) {
                    if ($this->isInsensitive) {
                        $productFieldReplaced = str_ireplace($this->search, $this->replace, $productField);
                    } else {
                        $productFieldReplaced = str_replace($this->search, $this->replace, $productField);
                    }

                    if (0 != strcasecmp($productFieldReplaced, $productField)) {
                        if ($this->isObjectBrick) {
                            $object->get($objectBrickKey)->get($this->field[0])->set($this->field[1], $productFieldReplaced);
                        } else {
                            $object->set($this->field[0], $productFieldReplaced);
                        }
                        $object->save();
                    }
                }
            } catch (Exception $e) {
                $this->returnAction(false, $e->getMessage());
            }
        }
    }

    private function returnAction(bool $success, string $msg): Response
    {
        return $this->json(
            [
                'success' => $success,
                'msg' => $msg,
            ],
            $success ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST
        );
    }
}
