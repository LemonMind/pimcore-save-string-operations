<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Controller;

use Exception;
use Lemonmind\SaveStringOperationsBundle\Services\ControllerService;
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
        $success = StringConcatService::stringConcat(
            $objectListing,
            $this->fields,
            $this->separator
        );

        return ControllerService::returnAction($success, '');
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
            $this->separator
        );

        return ControllerService::returnAction($success, '');
    }

    /**
     * @throws Exception
     */
    private function getParams(Request $request, bool $test = false): void
    {
        $language = $request->get('language');

        $fields = [];
        $fields[] = $request->get('field_one');
        $fields[] = $request->get('field_two');
        $fields[] = $request->get('field_save');
        $input_one = $request->get('input_one');
        $input_two = $request->get('input_two');

        $this->fields = ControllerService::getFields($fields, $language);

        if ('input' === $this->fields[0]['value']) {
            $this->fields[0]['value'] = $input_one;
            $this->fields[0]['type'] = 'input';
        }

        if ('input' === $this->fields[1]['value']) {
            $this->fields[1]['value'] = $input_two;
            $this->fields[1]['type'] = 'input';
        }

        $this->separator = $request->get('separator');
        $this->ids = array_filter(explode(',', trim($request->get('idList'))));

        $className = $request->get('className');
        $this->class = ControllerService::getClass($className);

        if (!class_exists($this->class)) {
            if ($test) {
                return;
            }
            ControllerService::returnAction(false, 'Class does not exist');
        }
    }
}
