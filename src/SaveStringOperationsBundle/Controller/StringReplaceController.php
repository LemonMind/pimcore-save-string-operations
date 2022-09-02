<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Controller;

use Exception;
use Lemonmind\SaveStringOperationsBundle\Services\ControllerService;
use Lemonmind\SaveStringOperationsBundle\Services\StringReplaceService;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/string_replace")
 */
class StringReplaceController extends AdminController
{
    private array $fields;
    private string $search;
    private string $replace;
    private bool $isInsensitive;
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
        $success = StringReplaceService::stringReplace(
            $objectListing,
            $this->fields,
            $this->search,
            $this->replace,
            $this->isInsensitive
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
        $success = StringReplaceService::stringReplace(
            $objectListing,
            $this->fields,
            $this->search,
            $this->replace,
            $this->isInsensitive
        );

        return ControllerService::returnAction($success, '');
    }

    /**
     * @throws Exception
     */
    private function getParams(Request $request, bool $test = false): void
    {
        $language = $request->get('language');

        $field = $request->get('field');
        $this->fields = ControllerService::getFields([$field], $language);

        $this->search = $request->get('search');
        $this->replace = $request->get('replace');
        $this->ids = array_filter(explode(',', trim($request->get('idList'))));
        $this->isInsensitive = null !== $request->get('insensitive');

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
