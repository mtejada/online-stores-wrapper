<?php

namespace App\Util;

use App\Util\ApiProblem;
use Symfony\Component\Config\Definition\Exception\Exception;
use Unirest\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializationContext;
use Psr\Log\LoggerInterface;
use JMS\Serializer\SerializerInterface;

class EbayConnector  {

    const headers = array('Accept' => 'application/json');

    private $serializer;
    private $baseUrl;
    private $ebayKey;
    private $logger;
    private $entityClassName;
    private $mappingParams;

    function __construct($baseUrl,$ebayKey, SerializerInterface $serializer, LoggerInterface $logger, $options = array()) {
        $this->serializer = $serializer;
        $this->baseUrl = $baseUrl;
        $this->ebayKey = $ebayKey;
        $this->logger = $logger;
        $this->mappingParams=array(
            'keywords'=>array(
                'type'=>'keywords',
            ),
            'price_max'=>array(
                'type'=>'itemFilter',
                'name'=>'MaxPrice'
            ),
            'price_min'=>array(
                'type'=>'itemFilter',
                'name'=>'MinPrice'
            ),
            'order'=>array(
              'type'=>'sort',
              'name'=>'sortOrder'
            ),
            'limit'=>array(
              'type'=>'limit'
            ),
            'page'=>array(
              'type'=>'page'
            ),
        );
       
    }

    /**
     * @return mixed
     */
    public function getBaseUrl() {
        return $this->baseUrl;
    }

    /**
     * @return mixed
     */
    public function getSerializer() {
        return $this->serializer;
    }


    /**
     * @return mixed
     */
    public function getLogger() {
        return $this->logger;
    }

    /**
     * @param mixed $logger
     */
    public function setLogger($logger) {
        $this->logger = $logger;
    }

   

    public function getUrl() {
        return $this->baseUrl;
    }



    /* GET FUNCTIONS */

    public function findBy($params) {

        $response = $this->baseGet($this->getUrl(), $params, "array<" . $this->entityClassName . ">", true);

        return $response;
    }



    public function baseGet( $params, $dataClass = null, $isPaginated = false) {
        $url=$this->baseUrl;
        $baseParams=array(
            'SERVICE-VERSION'=>'1.0.0',
            'SECURITY-APPNAME' => $this->ebayKey,
            'RESPONSE-DATA-FORMAT'=>'JSON',
            'REST-PAYLOAD'=>'true',
            'OPERATION-NAME'=>'findItemsByKeywords',
            'itemFilter(0).name'=>'ListingType',
            'itemFilter(0).value'=>'FixedPrice',
            'itemFilter(1).name'=>'Condition',
            'itemFilter(1).value'=>'New',
        );
        $nextItemFilter=2;
        $keywords="";
        $limit=50;
        $page=1;
        $sort="BestMatch";//def
        foreach($params as $key=> $value){
            if(isset($this->mappingParams[$key])){
                switch($this->mappingParams[$key]['type']){
                    case "itemFilter":
                        $baseParams['itemFilter('.$nextItemFilter.').name']=$this->mappingParams[$key]['name'];
                        $baseParams['itemFilter('.$nextItemFilter.').value']=$value;
                        $nextItemFilter++;
                        break;
                    case "keywords":                        
                        $keywords=$value;
                        break;
                    case "limit":                         
                        $limit=$value;
                        break;
                    case "page":                        
                        $page=$value;
                        break;
                    case "sort":   
                        if($value=="by_price_asc"){
                            $sort="PricePlusShippingLowest";
                        }
                        
                        break;
                    
                }
            }
        }
        $lastParams=array(
            'keywords'=>$keywords,
            'outputSelector(0)'=>'PictureURLSuperSize',
            'paginationInput(0).pageNumber'=>$page,
            'paginationInput(0).entriesPerPage'=>$limit,
            'sortOrder'=>$sort
        );
        $url = $url.'?';
        foreach(array_merge($baseParams,$lastParams) as  $key=>$value){
            $url.=$key."=".$value."&";
        }
        try {
           
            $response = Request::get($url, self::headers);
            
            return json_decode($response->raw_body,true)['findItemsByKeywordsResponse'];
            
        } catch (\Exception $ex) {
            $response = new ApiProblem();
            $response->setStatusCode(Response::HTTP_BAD_GATEWAY);
            $response->setExtraData($ex->getMessage());
            $response->setType("Connection Error");
        }

        return $response;
    }

    /* HANDLING FUNCTIONS */

  

}