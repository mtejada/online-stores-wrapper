<?php

namespace App\Util;

use Doctrine\Common\Collections\ArrayCollection;


class EntityMapper {

    private $connector;
    private $paymentMenuBaseUrl;

    public function __construct(\ApiBundle\Utils\ApiConnector $connector, string $paymentMenuBaseUrl) {
        $this->connector = $connector;
        $this->paymentMenuBaseUrl = $paymentMenuBaseUrl;
    }

    //transform this to the array convert based on store
    public function corePaymentMethodToPublic($corePaymentMethod){

        /** @var \AppBundle\Entity\PaymentMethod $paymentMethod */
        $paymentMethod = null;

        /** @var PaymentMethod $corePaymentMethod */
        if (!is_null($corePaymentMethod)){
            $paymentMethod = new \AppBundle\Entity\PaymentMethod();

            $paymentMethod->setName($corePaymentMethod->getName());
            $paymentMethod->setDisplayName($corePaymentMethod->getDisplayName());
        }

        return $paymentMethod;
    }

    public function corePaymentMethodGroupToPublic($corePaymentMethod){

        /** @var \AppBundle\Entity\PaymentMethodGroup $paymentMethod */
        $paymentMethod = null;

        /** @var PaymentMethod PaymentMethodGroup */
        if (!is_null($corePaymentMethod)){
            $paymentMethod = new \AppBundle\Entity\PaymentMethodGroup();

            $paymentMethod->setName($corePaymentMethod->getPublicName());
            $paymentMethod->setDisplayName($corePaymentMethod->getDisplayName());
        }

        return $paymentMethod;
    }
}