<?php

namespace App\ApiRepository;

use App\Util\EntityMapper;

class ProductRepository implements Repository {

    private $connector;
    private $mapper;

    public function __construct(\App\Util\EbayConnector $connector, EntityMapper $entityMapper) {
        $this->connector = $connector;
        $this->mapper = $entityMapper;
    }

    public function findByFilters($filter, $sort = "") {

        $products = $this->connector->BaseGet($filter);
        $transformedProducts = array();
        if ($products instanceof \App\Util\ApiProblem) {
            return $products;
        } else {
            $paginatedProducts = new\App\Util\Paginator();
            if (isset($products[0]['searchResult'][0]['item'])) {
                foreach ($products[0]['searchResult'][0]['item'] as $item) {
                    $product = $this->mapper->ebayToProduct($item);
                    $transformedProducts[] = $product;
                }
            }
            $paginatedProducts->setData($transformedProducts);
            $paginatedProducts->setPagination($products[0]['paginationOutput']);

            $paginatedProducts->initialize();
            return $paginatedProducts;
        }
    }

}
