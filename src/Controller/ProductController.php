<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\ApiRepository\ProductRepository;
use App\Util\Error;
use AppBundle\Form\Filter\PaymentFilterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Util\ApiProblem;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use App\Filter\ProductFilterType;
use App\Classes\ProductFilter;

class ProductController extends BaseApiController {

    public function __construct(ProductRepository $repo,ProductFilter $class) {
        parent::__construct(
               $repo,$class,"App\\Filter\\ProductFilterType"
        );
        $this->serializerGroups = array(
            "Default",
            //"products" => array("Default")
        );    
    }


    /**
     * @Rest\Get("/products", name="products_index")
     * @Rest\View()
     */
    public function indexAction(Request $request) {
        $response = $this->baseIndexAction($request, array('paginate' => true));
        return $response;
    }


}