<?php

namespace App\Handler;

class HandlerResult {

    const RESULT_OK = 200;
    const RESULT_CREATED = 201;
    const RESULT_VALIDATION_ERROR = 400;
    const RESULT_NOT_FOUND = 404;
    const RESULT_NO_CONTENT = 405;
    const RESULT_CONFLICT = 409;
    const RESULT_EXISTING_RELATED_ENTITY = 40901;
    const RESULT_ERROR = 500;

    private $data;
    private $resultCode;
    private $form;
    private $resultIsPaginated;

    public function __construct($resultCode, $form = null, $data = null) {
        $this->data = $data; //the object
        $this->resultCode = $resultCode;
        $this->form = $form;
    }

    /**
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data) {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getResultCode() {
        return $this->resultCode;
    }

    /**
     * @param mixed $resultCode
     */
    public function setResultCode($resultCode) {
        $this->resultCode = $resultCode;
    }

    /**
     * @return null
     */
    public function getForm() {
        return $this->form;
    }

    /**
     * @param null $form
     */
    public function setForm($form) {
        $this->form = $form;
    }

    /**
     * @return mixed
     */
    public function getResultIsPaginated() {
        return $this->resultIsPaginated;
    }

    /**
     * @param mixed $resultIsPaginated
     */
    public function setResultIsPaginated($resultIsPaginated) {
        $this->resultIsPaginated = $resultIsPaginated;
    }

}