<?php


namespace App\Classes;


use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("NONE")
 */
class Product {

    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\Groups({"response"})
     */
    private $provider="ebay"; 
    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\Groups({"response"})
     */
    private $itemId;
    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\Groups({"response"})
     */
    private $clickOutLink;
    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\Groups({"response"})
     */
    private $mainPhotoUrl;
    /**
     * @var string
     * @Serializer\Type("double")
     * @Serializer\Groups({"response"})
     */
    private $price;
    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\Groups({"response"})
     */
    private $priceCurrency;
    /**
     * @var string
     * @Serializer\Type("double")
     * @Serializer\Groups({"response"})
     */
    private $shippingPrice;
    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\Groups({"response"})
     */
    private $title;
    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\Groups({"response"})
     */
    private $description;
    /**
     * @var \DateTime
     * @Serializer\Type("DateTime")
     * @Serializer\Groups({"response"})
     */
    private $validUntil;
    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\Groups({"response"})
     */
    private $brand;

    public function getProvider(): string {
        return $this->provider;
    }

    public function getItemId(): string {
        return $this->itemId;
    }

    public function getClickOutLink(): string {
        return $this->clickOutLink;
    }

    public function getMainPhotoUrl(): string {
        return $this->mainPhotoUrl;
    }

    public function getPrice(): string {
        return $this->price;
    }

    public function getPriceCurrency(): string {
        return $this->priceCurrency;
    }

    public function getShippingPrice(): string {
        return $this->shippingPrice;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getValidUntil(): \DateTime {
        return $this->validUntil;
    }

    public function getBrand(): string {
        return $this->brand;
    }

    public function setProvider(string $provider) {
        $this->provider = $provider;
        return $this;
    }

    public function setItemId(string $itemId) {
        $this->itemId = $itemId;
        return $this;
    }

    public function setClickOutLink(string $clickOutLink) {
        $this->clickOutLink = $clickOutLink;
        return $this;
    }

    public function setMainPhotoUrl(string $mainPhotoUrl) {
        $this->mainPhotoUrl = $mainPhotoUrl;
        return $this;
    }

    public function setPrice(string $price) {
        $this->price = $price;
        return $this;
    }

    public function setPriceCurrency(string $priceCurrency) {
        $this->priceCurrency = $priceCurrency;
        return $this;
    }

    public function setShippingPrice(string $shippingPrice) {
        $this->shippingPrice = $shippingPrice;
        return $this;
    }

    public function setTitle(string $title) {
        $this->title = $title;
        return $this;
    }

    public function setDescription(string $description) {
        $this->description = $description;
        return $this;
    }

    public function setValidUntil(\DateTime $validUntil) {
        $this->validUntil = $validUntil;
        return $this;
    }

    public function setBrand(string $brand) {
        $this->brand = $brand;
        return $this;
    }


}
