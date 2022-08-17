<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringReplaceBundle\Controller;

use Exception;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/string_replace")
 */
class StringController extends AdminController
{
    private string $field;
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
        $productListing = new $this->class();
        $productListing->addConditionParam('o_id IN (?)', [$this->ids]);
        $this->stringReplace($productListing);

        return $this->returnAction(true, '');
    }

    /**
     * @Route("/all")
     */
    public function allAction(Request $request): Response
    {
        $this->getParams($request);
        $productListing = new $this->class();
        $this->stringReplace($productListing);

        return $this->returnAction(true, '');
    }

    private function getParams(Request $request): void
    {
        $this->field = $request->get('field');
        $this->search = $request->get('search');
        $this->replace = $request->get('replace');
        $className = $request->get('className');
        $this->ids = explode(',', $request->get('idList'));
        $this->isInsensitive = null !== $request->get('insensitive');

        $prefix = "\Pimcore\Model\DataObject";
        $suffix = '\Listing';
        $arr = explode(' ', "\ $className");
        $this->class = $prefix . $arr[0] . $arr[1] . $suffix;

        if (!class_exists($this->class)) {
            $this->returnAction(false, 'Class does not exist');
        }
    }

    private function stringReplace($productListing): void
    {
        foreach ($productListing as $product) {
            try {
                $productField = $product->get($this->field);

                if (null !== $productField) {
                    if ($this->isInsensitive) {
                        $productFieldReplaced = str_ireplace($this->search, $this->replace, $productField);
                    } else {
                        $productFieldReplaced = str_replace($this->search, $this->replace, $productField);
                    }

                    if (0 != strcasecmp($productFieldReplaced, $productField)) {
                        $product->set($this->field, $productFieldReplaced);
                        $product->save();
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
