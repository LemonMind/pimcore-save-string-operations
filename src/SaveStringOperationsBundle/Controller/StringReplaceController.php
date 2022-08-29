<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringOperationsBundle\Controller;

use Exception;
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
    private array $field;
    private string $search;
    private string $replace;
    private bool $isInsensitive;
    private bool $isObjectBrick = false;
    private bool $isClassificationStore = false;
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
            $this->field,
            $this->search,
            $this->replace,
            $this->isInsensitive,
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
        $success = StringReplaceService::stringReplace(
            $objectListing,
            $this->field,
            $this->search,
            $this->replace,
            $this->isInsensitive,
            $this->isObjectBrick,
            $this->isClassificationStore
        );

        return $this->returnAction($success, '');
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

            if ('classificationstore' === $this->field[1]) {
                $this->isClassificationStore = true;
            } else {
                $this->isObjectBrick = true;
            }
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
