<?php

namespace App\Handler;

use Symfony\Component\Form\FormInterface;


class CommonHandler {

    protected $formFactory;
    protected $logger;
    protected $customPagination;
    protected $formType;
    protected $entityRepository;

    public function __construct($formFactory, $logger, $paginator, $customPagination) {

        $this->formFactory = $formFactory;
        $this->logger = $logger;
        $this->paginator = $paginator;
        $this->customPagination = $customPagination;
    }

    /**
     * @return mixed
     */
    public function getFormType() {
        return $this->formType;
    }

    /**
     * @param mixed $formType
     */
    public function setFormType($formType) {
        $this->formType = $formType;
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

    /**
     * @return mixed
     */
    public function getEntityClass() {
        return $this->getEntityRepository()->getEntityClass();
    }

    public function show($params) {
        $object = $this->getEntityRepository()->findActive($params);

        $object = $this->getEntityRepository()->postShow($object, $params);

        if ($object) {
            return new HandlerResult(HandlerResult::RESULT_OK, null, $object);
        } else {
            return new HandlerResult(HandlerResult::RESULT_NOT_FOUND);
        }
    }

    public function retrieve($params) {

        $paginate = $params['paginate'];
        $data = $params['payload'];
        $order = isset($params['payload']['order']) ? $params['payload']['order'] : ""; // if its set, then use it...
        //force the filters in the repo, to allow internal customization
        //if ($paginate) {
        $results = $this->getEntityRepository()->findByFilters($data,
                (isset($data['page']) ? $data['page'] : 1), // page number
                (isset($data['limit']) ? $data['limit'] : 10)    // limit per page
        );

        $results = $this->getEntityRepository()->postRetrieve($results, $params);
        //$this->customPagination->setPaginatedResults($results);

        $result = new HandlerResult(HandlerResult::RESULT_OK, null, $results);

        return $result;
    }

    public function insert($params) {

        $data = $params['payload'];

        $object = $this->getEntityRepository()->getEntityInstance($data);
        $formType = get_class($this->formType);
        $form = $this->formFactory->create($formType, $object);

        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getEntityRepository()->preInsert($object, $data);

            //check for duplicity
            $existing = $this->getEntityRepository()->searchExisting($object);

            if (!$existing) {
                $object = $this->getEntityRepository()->persist($object);

                return new HandlerResult(HandlerResult::RESULT_CREATED, $form, $object);
            } else {
                //another object already exists!
                return new HandlerResult(HandlerResult::RESULT_CONFLICT, $form, $existing);
            }
        } else {
            return new HandlerResult(HandlerResult::RESULT_VALIDATION_ERROR, $form, $object);
        }
    }

    public function edit($params) {

        $id = $params['id'];
        $data = $params['payload'];

        $object = $this->getEntityRepository()->find($id);
        if ($object) {
            $formType = get_class($this->formType);
            $form = $this->formFactory->create($formType, $object);

            $clearFields = false; //Treat the missing fields as null? As this is a PATCH we dont want to!

            $form->submit($data, $clearFields);
            if ($form->isSubmitted() && $form->isValid()) {

                $this->getEntityRepository()->preUpdate($object, $data);

                //check for existence now that the data is changed
                $existing = $this->getEntityRepository()->searchExisting($object);

                if (!$existing) {

                    $this->getEntityRepository()->persist($object);


                    return new HandlerResult(HandlerResult::RESULT_OK, $form, $object);
                } else {
                    //another object already exists!
                    return new HandlerResult(HandlerResult::RESULT_CONFLICT, $form, $existing);
                }
            } else {
                return new HandlerResult(HandlerResult::RESULT_VALIDATION_ERROR, $form, $object);
            }
        } else {
            return new HandlerResult(HandlerResult::RESULT_NOT_FOUND);
        }
    }

    public function delete($params) {
        
        $id = $params['id'];
        $object = $this->getEntityRepository()->find($id);

        if ($object) {
            if ($this->getEntityRepository()->canDelete($object)) {
                //internally, this delete updates where it needs to, to set this info as "deleted"
                $this->getEntityRepository()->delete($object);
                return new HandlerResult(HandlerResult::RESULT_NO_CONTENT);
            } else {

                return new HandlerResult(HandlerResult::RESULT_CONFLICT, null, null);
            }
        } else {
            return new HandlerResult(HandlerResult::RESULT_NOT_FOUND);
        }
    }

    public function getErrorsFromFormAsString($form) {

        $errors = $this->getErrorsFromForm($form);

        return json_encode($errors);
    }

    public function getErrorsFromForm($form) {

        $errors = array();
        foreach ($form->getErrors() as $error) {
            $params = $error->getMessageParameters();
            if (isset($params['{{ extra_fields }}'])) {
                $errors[] = $error->getMessage() . ": " . str_replace("\"", "", $params['{{ extra_fields }}']);
            } else {
                $cause = $error->getCause();
                if (is_null($cause)) {
                    $errors[] = $error->getMessage();
                } else {
                    $errors[] = $error->getMessage() . ": " . $this->extractPropertyName($cause->getPropertyPath());
                }
            }
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }


        return $errors;
    }

    /*
     * Sometimes (didn't do an exhaustive research) the property name
     * contains a prefix of "data.", so here we "sanitize" it and return
     * the correct name
     */

    private function extractPropertyName($name) {

        $prefix = 'data.';

        if (substr($name, 0, strlen($prefix)) == $prefix) {
            $name = substr($name, strlen($prefix));
        }

        $prefix = 'children[';

        if (substr($name, 0, strlen($prefix)) == $prefix) {
            $name = substr($name, strlen($prefix), -1);
        }


        return $name;
    }

}