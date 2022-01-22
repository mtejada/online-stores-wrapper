<?php

namespace App\Util;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiProblemException extends HttpException {

    private $apiProblem;

    public function __construct(ApiProblem $apiProblem, \Exception $previous = null, array $headers = array(), $code = 0) {

        $this->apiProblem = $apiProblem;
        $statusCode = $apiProblem->getStatusCode();
        $message = $apiProblem->getTitle();

        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

    /**
     * @return ApiProblem
     */
    public function getApiProblem() {
        return $this->apiProblem;
    }

    /**
     * @param ApiProblem $apiProblem
     */
    public function setApiProblem($apiProblem) {
        $this->apiProblem = $apiProblem;
    }

}