<?php

declare(strict_types=1);

namespace Lemonmind\GridOptionsSaveBundle\Controller;

use Exception;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GridOptionsController extends AdminController
{
    /**
     * @Route("/admin/string_replace")
     */
    public function indexAction(Request $request): Response
    {

        //$class = $request->get('class');
        $className = "Car";


        $prefix = "\Pimcore\Model\DataObject";

        $arr = explode(' ', "\ $className");
        $class = $prefix . $arr[0] . $arr[1] . '\Listing';
        //dd($cos);
        //       if (!class_exists($class)) {
//            return $this->returnAction(false, 'Class does not exist');
//        }

        $productListing = new $class();

        $data = [];

        //dd($productListing);
        foreach ($productListing as $product) {
            try {
//                $product->setName('some text');
//                $product->save();
            } catch (Exception $e) {
                return $this->json(
                    [
                        'error' => $e->getMessage(),
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
            ];
        }


        return $this->json(
            [
                'data' => $data,
            ],
            Response::HTTP_OK
        );
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
