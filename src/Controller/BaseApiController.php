<?php

namespace App\Controller;

use App\Handler\CommonHandler;
use App\Handler\HandlerResult;
use App\Util\ApiProblem;
use App\Util\ApiProblemException;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Context\Context;

class BaseApiController extends FOSRestController {

    protected $formType;
    protected $entityRepository;
    protected $queryBuilderAlias = "t";

    /** @var \AppBundle\Handler\CommonHandler $entityHandler */
    protected $entityHandler;
    //Subclasses must edit this to customize the return data
    protected $serializerGroups = array("Default");

    protected function __construct($formType, $entityRepository) {
        $this->formType = $formType;
        $this->entityRepository = $entityRepository;
    }

    /**
     * @return mixed
     */
    protected function getFormType() {
        return $this->formType;
    }

    /**
     * @param mixed $formType
     */
    protected function setFormType($formType) {
        $this->formType = $formType;
    }

    /**
     * @return mixed
     */
    protected function getEntityClass() {
        return $this->entityRepository;
    }

    /**
     * @param mixed $entityRepository
     */
    protected function setEntityClass($entityRepository) {
        $this->entityRepository = $entityRepository;
    }

    /**
     * @return mixed
     */
    public function getQueryBuilderAlias() {
        return $this->queryBuilderAlias;
    }

    /**
     * @param mixed $queryBuilderAlias
     */
    public function setQueryBuilderAlias($queryBuilderAlias) {
        $this->queryBuilderAlias = $queryBuilderAlias;
    }

    /**
     * @return array
     */
    public function getSerializerGroups() {
        return $this->serializerGroups;
    }

    /**
     * @param array $serializerGroups
     */
    public function setSerializerGroups($serializerGroups) {
        $this->serializerGroups = $serializerGroups;
    }

    /**
     * @return \AppBundle\Handler\CommonHandler
     */
    public function getEntityHandler() {
        if (!$this->entityHandler) {

            //initialize it. We cannot do this in the constructor because we need to access the container, which
            //is not available yet.
            //And we can't pass it as an arg because we need to set the entity class and form type to be able to use it,
            //but those parameters are set in the classes that extends this base class.

            /** @var \AppBundle\Handler\CommonHandler $entityHandler */
            $entityHandler = $this->get('App\\Handler\\CommonHandler');
            $entityHandler->setEntityRepository($this->entityRepository);
            $entityHandler->setFormType($this->formType);

            $this->entityHandler = $entityHandler;
        }
        return $this->entityHandler;
    }

    protected function throwApiValidationException($form) {
        $this->throwApiProblemException(
                Response::HTTP_BAD_REQUEST,
                ApiProblem::TYPE_VALIDATION_ERROR,
                array('form' => $form)
        );
    }

    protected function buildNotAuthorizedException() {
                $this->throwApiProblemException(
                Response::HTTP_UNAUTHORIZED,
                ApiProblem::NOT_AUTHORIZED,
                        array('description'=>'the api-key is invalid')
        );
        
    }

    protected function throwApiObjectNotFoundException($description = null) {
        $this->throwApiProblemException(
                Response::HTTP_NOT_FOUND,
                ApiProblem::TYPE_NOT_FOUND,
                array(
                    'description' => $description
                )
        );
    }

    protected function throwApiProblemException($status, $type, $options = array()) {

        $form = isset($options['form']) ? $options['form'] : null;
        $description = isset($options['description']) ? $options['description'] : null;
        $exception = isset($options['exception']) ? $options['exception'] : null;

        $apiProblem = new ApiProblem(
                $status,
                $type
        );

        $errors = array();
        if ($form) {
            $formErrors = $this->entityHandler->getErrorsFromForm($form);
            $errors = $formErrors;
        }

        if ($description) {
            $errors[] = $description;
        }
        $apiProblem->set('errors', $errors);

        //This annotation is a workaround for a intellij bug!
        //"The thrown object must be an instance of the Exception or Throwable"
        /** @var \Exception $apiProblemEx */
        $apiProblemEx = new ApiProblemException(
                $apiProblem,
                $exception,
                array('Content-Type' => 'application/problem+json'),
                $apiProblem->getStatusCode()
        );
        

        throw $apiProblemEx;
    }

    protected function baseIndexAction(Request $request, $options = array()) {
        $arrComp = $this->extractCompany($request);
        if (!$arrComp) {

            $response = $this->buildNotAuthorizedException();
        } else {
            $paginateResults = isset($options['paginate']) ? $options['paginate'] : true;

            $params['payload'] = $request->query->all();
            $params['paginate'] = $paginateResults;

            $params['payload'] = array_merge($params['payload'], $arrComp);
            //If the filters options is set, then merge those too
            if (isset($options['filters'])) {
                $params['payload'] = array_merge($params['payload'], $options['filters']);
            }

            /** @var \AppBundle\Handler\HandlerResult $handlerResult */
            $handlerResult = $this->getEntityHandler()->retrieve($params);

            $response = $this->processHandlerResult($handlerResult, $request);

            $response->setContext($this->getSerializerContext(true));
        }
        return $response;
    }

    protected function processHandlerResult(HandlerResult $handlerResult, $request) {
        $this->get('logger')->debug($handlerResult->getResultCode());

        switch ($handlerResult->getResultCode()) {
            case HandlerResult::RESULT_CREATED: {

                    $response = $this->view($handlerResult->getData(), Response::HTTP_CREATED);
                    return $response;
                    break;
                }
            case HandlerResult::RESULT_OK: {

                    $response = $this->view($handlerResult->getData(), Response::HTTP_OK);

                    return $response;
                    break;
                }
            case HandlerResult::RESULT_CONFLICT:
            case HandlerResult::RESULT_EXISTING_RELATED_ENTITY: {

                    $this->get('logger')->debug($request->getContent());
                    if (!is_null($handlerResult->getData())) {
                        $this->get('logger')->debug("Conflict: Existing object detected: " . $handlerResult->getData()->getId());

                        $this->throwApiProblemException(
                                Response::HTTP_CONFLICT,
                                ApiProblem::TYPE_EXISTING_OBJECT,
                                array(
                                    'form' => $handlerResult->getForm(),
                                    'description' => "Existing object detected"
                                )
                        );
                    } else {
                        $this->get('logger')->debug("Conflict: " . $handlerResult->getResultCode());

                        $this->throwApiProblemException(
                                Response::HTTP_CONFLICT,
                                ApiProblem::TYPE_EXISTING_RELATED_ENTITY
                        );
                    }


                    break;
                }
            case HandlerResult::RESULT_NOT_FOUND: {

                    $this->throwApiObjectNotFoundException();
                    break;
                }
            case HandlerResult::RESULT_VALIDATION_ERROR: {
                    $this->get('logger')->debug($this->entityHandler->getErrorsFromFormAsString($handlerResult->getForm()));
                    $this->get('logger')->debug($request->getContent());

                    $this->throwApiProblemException(
                            Response::HTTP_BAD_REQUEST,
                            ApiProblem::TYPE_VALIDATION_ERROR,
                            array(
                                'form' => $handlerResult->getForm()
                            )
                    );

                    break;
                }
            case HandlerResult::RESULT_NO_CONTENT: {
                    $response = $this->view($handlerResult->getData(), Response::HTTP_NO_CONTENT);
                    return $response;
                    break;
                }
            default: {
                    $this->throwApiProblemException(Response::HTTP_INTERNAL_SERVER_ERROR, ApiProblem::TYPE_ERROR);
                }
        }
    }

    //the $paginated parameter is TRUE when the response is paginated, as the objects are in a property called "items"
    //which is INSIDE a CustomPagination object, so we must target THAT field in order to properly apply the serialization
    protected function getSerializerContext($paginated = false) {

        $context = new Context();
        if ($paginated) {
            $context->setGroups(
                    array(
                        'public-api',
                        'data' => $this->serializerGroups
                    )
            );
        } else {
            $context->setGroups($this->serializerGroups);
        }

        return $context;
    }

    public function extractCompany($request) {
        $res = false;
        if (is_numeric($request->headers->get('x-consumer-custom-id'))) {

            $res['company'] = $request->headers->get('x-consumer-custom-id');
            $res['username'] = $request->headers->get('x-consumer-username');
            $res['groups'] = $request->headers->get('x-consumer-groups');
            $res['key'] = $request->headers->get('x-api-key');
        } else {
            //$this->throwApiNotAuthorizedException($request->headers->get('x-api-key'));
        }
        return $res;
    }

}