<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Controller;

use Exception;
use Lemonmind\SaveStringOperationsBundle\Services\StringConcatService;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
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
    private string|array $fieldToSaveConcat;
    private string $separator;
    private string $class;
    private bool $isObjectBrick = false;
    private bool $isClassificationStore = false;
    private array $ids;

    /**
     * @Route("/selected")
     */
    public function selectedAction(Request $request): Response
    {
        $this->getParams($request);
        $objectListing = new $this->class();
        $objectListing->addConditionParam('o_id IN (?)', [$this->ids]);
        $success = StringConcatService::stringConcat(
            $objectListing,
            $this->fields,
            $this->userInput,
            $this->fieldToSaveConcat,
            $this->separator,
            $this->isObjectBrick,
            $this->isClassificationStore
        );

        return $this->returnAction($success, '');
    }

    /**
     * @Route("/all")
     */
    public function allAction(Request $request): Response
    {
        $this->getParams($request);
        $objectListing = new $this->class();
        $success = StringConcatService::stringConcat(
            $objectListing,
            $this->fields,
            $this->userInput,
            $this->fieldToSaveConcat,
            $this->separator,
            $this->isObjectBrick,
            $this->isClassificationStore,
        );

        return $this->returnAction($success, '');
    }

    /**
     * @throws Exception
     */
    private function getParams(Request $request, bool $test = false): void
    {
        $this->fields[] = $request->get('field_one');
        $this->fields[] = $request->get('field_two');
        $this->fieldToSaveConcat = $request->get('field_save');
        $this->userInput = '';

        if (str_contains($this->fields[0], '~')) {
            $this->fields[0] = explode('~', $this->fields[0]);

            if ('classificationstore' == $this->fields[0][1]) {
                $this->isClassificationStore = true;
            } else {
                $this->isObjectBrick = true;
            }
        }

        if (str_contains($this->fields[1], '~')) {
            $this->fields[1] = explode('~', $this->fields[1]);

            if ('classificationstore' == $this->fields[1][1]) {
                $this->isClassificationStore = true;
            } else {
                $this->isObjectBrick = true;
            }
        }

        if (str_contains($this->fieldToSaveConcat, '~')) {
            $this->fieldToSaveConcat = explode('~', $this->fieldToSaveConcat);

            if ('classificationstore' == $this->fieldToSaveConcat[1]) {
                $this->isClassificationStore = true;
            } else {
                $this->isObjectBrick = true;
            }
        }

        if ('input' === $this->fields[0]) {
            $this->userInput = $request->get('input_one');
        }

        if ('input' === $this->fields[1]) {
            $this->userInput = $request->get('input_two');
        }

        $this->separator = $request->get('separator');
        $this->ids = array_filter(explode(',', trim($request->get('idList'))));
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
