<?php

namespace App\Util;

use Doctrine\Common\Collections\ArrayCollection;

class EntityMapper {

    public function ebayToProduct($item) {

        /** @var \App\Classes\Product $product */
        $product = new \App\Classes\Product();

        $product->setItemId( ($item['itemId'][0]));
        $product->setClickOutLink($item['viewItemURL'][0]);
        $product->setMainPhotoUrl(isset($item['pictureURLSuperSize']) ? $item['pictureURLSuperSize'][0] : 'NO_PHOTO' ); //placeholder if does not exist?
        $product->setPriceCurrency($item['sellingStatus'][0]['currentPrice'][0]['@currencyId']);
        $product->setPrice($item['sellingStatus'][0]['currentPrice'][0]['__value__']);
        $product->setShippingPrice($item['shippingInfo'][0]['shippingServiceCost'][0]['__value__']);
        $product->setTitle($item['title'][0]);
        $product->setValidUntil(\DateTime::createFromFormat(\DateTime::RFC3339_EXTENDED, $item['listingInfo'][0]['endTime'][0]));
        $product->setBrand(''); 
        $product->setDescription('');
   
        return $product;
    }

}
