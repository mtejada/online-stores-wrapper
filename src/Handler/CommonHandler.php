<?php

namespace App\Handler;

use Symfony\Component\Form\FormInterface;


class CommonHandler {

    protected $logger;
    protected $customPagination;
    protected $formType;
    protected $entityRepository;

    public function __construct($logger, $paginator, $customPagination) {

        $this->logger = $logger;
        $this->paginator = $paginator;
        $this->customPagination = $customPagination;
    }

    /**
     * @return mixed
     */
    public function getEntityRepository() {
        return $this->entityRepository;
    }

    /**
     * @param mixed $entityRepository
     */
    public function setEntityRepository($entityRepository) {
        $this->entityRepository = $entityRepository;
    }


    public function retrieve($params) {

        $paginate = $params['paginate'];
        $data = $params['payload'];
        $order = isset($params['payload']['order']) ? $params['payload']['order'] : ""; // if its set, then use it...

        $results = $this->getEntityRepository()->findByFilters($data);
      

        $result = new HandlerResult(HandlerResult::RESULT_OK, null, $results);
        $result->setResultIsPaginated(true);
    
        return $result;
    }



}