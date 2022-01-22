<?php

namespace AppBundle\Repository;

use AppBundle\Util\EntityMapper;

/**
 * Description of ClientRepository
 */
class ClientRepository implements Repository {

    private $connector;
    private $mapper;

    public function __construct(\ApiBundle\Utils\ApiConnector $connector, EntityMapper $entityMapper) {
        $this->connector = $connector;
        $this->mapper = $entityMapper;
    }



    public function find($id) {
        /*$client = $this->connector->getApiRepository('Client')->find($id);
        return $this->mapper->coreClientToPublic($client);*/
    }

    public function findActive($params) {
        /*$client = $this->connector->getApiRepository('Client')->findOneBy($params);
        return $this->mapper->coreClientToPublic($client);*/
    }

    public function findByFilters($filter, $page = 1, $limit = 10, $sort = "") {
        $filter = array_merge($filter, array('limit' => $limit));
        $filter = array_merge($filter, array('page' => $page));
        $coreClients = $this->connector->getApiRepository('Client')->findBy($filter);
        $clients = array();
        if ($coreClients instanceof \ApiBundle\Utils\ApiProblem) {
            return $coreClients;
        } else {
            /** @var \ApiBundle\Entity\Client  $coreClient */
            foreach ($coreClients->getData() as $coreClient) {
                $client = $this->mapper->coreClientToPublic($coreClient);
                $clients[] = $client;
            }
            $coreClients->setData($clients);
            return $coreClients;
        }
    }

    public function getEntityClass() {
        return \AppBundle\Entity\Client::class;
    }

    public function getEntityInstance($args) {
        if (isset($args['id'])) {
            return $this->find($args['id']);
        } else {
            return new \AppBundle\Entity\Client();
        }

        return $this->findActive($params);
    }


}