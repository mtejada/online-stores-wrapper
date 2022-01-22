<?php

namespace App\ApiRepository;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Repository
 *
 * @author mtejada
 */
interface Repository {

    public function findActive($params);
    public function getEntityClass();
    public function postShow($object, $params);
    public function findByFilters($filter,$page=1,$limit=10,$sort="");//model with data? or form?
    public function postRetrieve($results, $params);
    public function getEntityInstance($params);
    
}