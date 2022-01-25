<?php

namespace App\Classes;

use Symfony\Component\Validator\Constraints as Assert;

class ProductFilter {


    private $keywords;
    private $minPrice;
    private $maxPrice;

    public function getKeywords() {
        return $this->keywords;
    }

    public function getMinPrice() {
        return $this->minPrice;
    }

    public function getMaxPrice() {
        return $this->maxPrice;
    }

    public function setKeywords($keywords) {
        $this->keywords = $keywords;
        return $this;
    }

    public function setMinPrice($minPrice) {
        $this->minPrice = $minPrice;
        return $this;
    }

    public function setMaxPrice($maxPrice) {
        $this->maxPrice = $maxPrice;
        return $this;
    }
    /**
     *  @Assert\IsFalse(message="keywords must not be empty")
     */
    public function isEmptyKeywords() {
        return $this->keywords=="";
    }

    /**
     *  @Assert\IsTrue(message="range must be valid")
     */
    public function isPriceValid() {

        if (!is_null($this->minPrice) || !is_null($this->maxPrice)) {
            if (!is_double($this->maxPrice)) {
                return false;
            }
            if (!is_double($this->maxPrice)) {
                return false;
            }
            if ($this->minPrice > $this->maxPrice) {
                return false;
            }
            
        }
        return true;
    }

}
