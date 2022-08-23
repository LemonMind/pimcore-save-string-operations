<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringReplaceBundle\Controller;

use Exception;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Pimcore\Model\DataObject\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/string_concat")
 */
class StringConcatController extends AdminController
{
    private array $fields;
    private string $userInput;
    private string $fieldToSaveConcat;
    private string $separator;
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
        $this->stringConcat($objectListing);

        return $this->returnAction(true, '');
    }

    /**
     * @Route("/all")
     */
    public function allAction(Request $request): Response
    {
        $this->getParams($request);
        // $objectListing = new $this->class();
        $objectListing = new Event\Listing();

        $this->stringConcat($objectListing);

        return $this->returnAction(true, '');
    }

    /**
     * @throws Exception
     */
    private function getParams(Request $request, bool $test = false): void
    {
        $this->fields = ['title', ''];
        $this->separator = ' ';
        $this->fieldToSaveConcat = 'title';
        $this->userInput = 'cos';

        // $this->fields = $request->get('fields');
        // $this->userInput = $request->get('userInput');
        // $this->isUserInputFirst = $request->get('isUserInputFirst');
        // $this->fieldToSaveConcat = $request->get('fieldToSaveConcat');
        // $this->separator = $request->get('separator');
        // $this->ids = array_filter(explode(',', trim($request->get('idList'))));
        $className = $request->get('className');

        if ('' === $className) {
            throw new Exception('Class name is not defined');
        }

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

    private function stringConcat($objectListing): void
    {
        foreach ($objectListing as $object) {
            try {
                if ('' !== $this->userInput) {
                    if ('' === $this->fields[0]) {
                        $fields[] = $this->userInput;
                        $fields[] = $object->get($this->fields[1]);
                    } else {
                        $fields[] = $object->get($this->fields[0]);
                        $fields[] = $this->userInput;
                    }
                } else {
                    $fields[] = $object->get($this->fields[0]);
                    $fields[] = $object->get($this->fields[1]);
                }
                $field = $fields[0] . $this->separator . $fields[1];
                $object->set($this->fieldToSaveConcat, $field);
                $object->save();
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
