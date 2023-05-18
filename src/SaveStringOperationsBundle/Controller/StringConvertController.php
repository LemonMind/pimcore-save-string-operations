<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Controller;

use Exception;
use Lemonmind\SaveStringOperationsBundle\Services\ControllerService;
use Lemonmind\SaveStringOperationsBundle\Services\StringConvertService;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/string_convert")
 */
class StringConvertController extends FrontendController
{
    private array $fields;
    private string $type;
    private string $class;
    private array $ids;

    /**
     * @Route("/selected")
     */
    public function selectedAction(Request $request): Response
    {
        $this->getParams($request);
        $objectListing = new $this->class();
        $objectListing->addConditionParam('id IN (?)', [$this->ids]);
        $success = StringConvertService::stringConvert($objectListing, $this->fields, $this->type);

        return ControllerService::returnAction($success, '');
    }

    /**
     * @Route("/all")
     */
    public function allAction(Request $request): Response
    {
        $this->getParams($request);
        $objectListing = new $this->class();
        $success = StringConvertService::stringConvert($objectListing, $this->fields, $this->type);

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

        $this->type = $request->get('type');
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
