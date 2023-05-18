<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Controller;

use App\Model\Product\Car;
use Exception;
use Lemonmind\SaveStringOperationsBundle\Services\ControllerService;
use Lemonmind\SaveStringOperationsBundle\Services\NumberOperationsService;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/number_change")
 */
class NumberOperationsController extends FrontendController
{
    private array $fields;
    private string $setTo;
    private float $value;
    private string $changeType = '';
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
        $success = NumberOperationsService::numberOperations(
            $objectListing,
            $this->fields,
            $this->setTo,
            $this->value,
            $this->changeType
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
        $success = NumberOperationsService::numberOperations(
            $objectListing,
            $this->fields,
            $this->setTo,
            $this->value,
            $this->changeType
        );

        return ControllerService::returnAction($success, '');
    }

    /**
     * @throws Exception
     */
    private function getParams(Request $request, bool $test = false): void
    {
        $field = $request->get('field');
        $this->fields = ControllerService::getFields([$field]);

        $this->setTo = $request->get('set_to');
        $this->value = (float) $request->get('value');
        $this->ids = array_filter(explode(',', trim($request->get('idList'))));

        if ('percentage' === $this->setTo) {
            $this->changeType = $request->get('change_type');
            $this->value = $this->value / 100;
        }

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
