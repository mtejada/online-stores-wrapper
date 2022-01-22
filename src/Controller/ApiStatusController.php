<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

class ApiStatusController{

    /**
     * Check API status.
     *
     * @Rest\Get("/healthcheck", name="check_status_api")
     * @Rest\View()
     */
    public function healtcheck() {
        $response = new Response('API Online', Response::HTTP_OK);
        return $response;
    }

}