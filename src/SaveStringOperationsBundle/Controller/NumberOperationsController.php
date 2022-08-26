<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Controller;

use Exception;
use Lemonmind\SaveStringOperationsBundle\Services\NumberOperationsService;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/number_change")
 */
class NumberOperationsController extends AdminController
{
    private array $field;
    private string $setTo;
    private float $value;
    private string $changeType = '';
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
        $success = NumberOperationsService::numberOperations(
            $objectListing,
            $this->field,
            $this->setTo,
            $this->value,
            $this->changeType,
            $this->isObjectBrick
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
        $success = NumberOperationsService::numberOperations(
            $objectListing,
            $this->field,
            $this->setTo,
            $this->value,
            $this->changeType,
            $this->isObjectBrick
        );

        return $this->returnAction($success, '');
    }

    /**
     * @throws Exception
     */
    private function getParams(Request $request, bool $test = false): void
    {
        $this->field[] = $request->get('field');
        $this->setTo = $request->get('set_to');
        $this->value = (float) $request->get('value');
        $className = $request->get('className');
        $this->ids = array_filter(explode(',', trim($request->get('idList'))));

        if (str_contains($this->field[0], '~')) {
            $this->field = explode('~', $this->field[0]);
            $this->isObjectBrick = true;
        }

        if ('percentage' === $this->setTo) {
            $this->changeType = $request->get('change_type');
            $this->value = $this->value / 100;
        }

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