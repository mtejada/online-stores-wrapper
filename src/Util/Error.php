<?php

namespace App\Util;

use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Serializer\ExclusionPolicy("NONE")
 */
class Error {

    /**
     * @var string
     * @Serializer\Type("integer")
     * @Serializer\Groups({"Default"})
     */
    private $status;

    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\Groups({"Default"})
     */
    protected $type;

    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\Groups({"Default"})
     */
    private $title;

    /**
     * @var string
     * @Serializer\Type("array")
     * @Serializer\Groups({"Default"})
     */
    private $errors;
    public function getStatus() {
        return $this->status;
    }

    public function getType() {
        return $this->type;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setErrors($errors) {
        $this->errors = $errors;
    }


}