<?php

namespace App\ApiRepository;


interface Repository {

    public function findByFilters($filter,$sort="");//model with data? or form
    
}