<?php

namespace App\Util;

use Symfony\Component\HttpFoundation\Response;

/**
 * A wrapper for holding data to be used for a application/problem+json response
 * https://knpuniversity.com/screencast/symfony-rest2/api-problem-class#play
 */
class ApiProblem {

    const NOT_AUTHORIZED = 'not_authorized';
    const TYPE_VALIDATION_ERROR = 'validation_error';
    const TYPE_INVALID_PARAMETERS_ERROR = 'invalid_parameters';
    const TYPE_INVALID_REQUEST_BODY_FORMAT = 'invalid_body_format';
    const TYPE_MISSING_VALUES = 'missing_values';
    const TYPE_EXISTING_OBJECT = 'existing_object';
    const TYPE_INVALID_CURRENT_PASSWORD = 'invalid_password';
    const TYPE_INVALID_CREDENTIALS = 'invalid_credentials';
    const TYPE_PASSWORD_CHANGE_REQUIRED = 'change_password_required';
    const TYPE_PASSWORD_CHANGE_TOO_SOON = 'denied_change_password_too_soon';
    const TYPE_PASSWORD_CHANGE_OLD_USED = 'denied_old_password_used';
    const TYPE_PASSWORD_NOT_STRONG_ENOUGH = 'password_not_enough_strong';
    const TYPE_SESSION_LOCKED = 'session_locked';
    const TYPE_NOT_FOUND = 'not_found';
    const TYPE_EXISTING_RELATED_ENTITY = 'existing_related_entity_exists';
    const TYPE_ERROR = 'error';

    private static $titles = array(
        self::TYPE_VALIDATION_ERROR => 'There was a validation error',
        self::TYPE_INVALID_PARAMETERS_ERROR => 'Invalid parameters received',
        self::TYPE_INVALID_REQUEST_BODY_FORMAT => 'Invalid JSON format sent',
        self::TYPE_MISSING_VALUES => 'Unable to continue. Mandatory values are missing.',
        self::TYPE_EXISTING_OBJECT => 'Unable to continue. Object already exists',
        self::TYPE_INVALID_CURRENT_PASSWORD => 'Invalid current password',
        self::TYPE_INVALID_CREDENTIALS => 'Invalid credentials',
        self::TYPE_PASSWORD_CHANGE_REQUIRED => 'Change password required',
        self::TYPE_PASSWORD_CHANGE_TOO_SOON => 'Denied change password too soon',
        self::TYPE_PASSWORD_CHANGE_OLD_USED => 'Denied old password used',
        self::TYPE_PASSWORD_NOT_STRONG_ENOUGH => 'Password not strong enough',
        self::TYPE_SESSION_LOCKED => 'Session locked',
        self::TYPE_NOT_FOUND => 'Object not found',
        self::TYPE_ERROR => 'Undefined error',
        self::TYPE_EXISTING_RELATED_ENTITY => 'Unable to continue. Another related entity exists',
        self::NOT_AUTHORIZED => 'Not authorized'
    );

    /**
     * @var string
     *
     */
    private $statusCode;

    /**
     * @var string
     *
     */
    private $type;

    /**
     * @var string
     *
     */
    private $title;

    /**
     * @var array
     *
     */
    private $extraData = array();

    public function __construct($statusCode, $type = null) {

        if ($type === null) {

            // no type? The default is about:blank and the title should
            // be the standard status code message
            $type = 'about:blank';

            $title = isset(Response::$statusTexts[$statusCode]) ? Response::$statusTexts[$statusCode] : 'Unknown status code :(';
        } else {


            if (!isset(self::$titles[$type])) {
                throw new \InvalidArgumentException('No title for type ' . $type);
            }

            $title = self::$titles[$type];
        }
        $this->statusCode = $statusCode;
        $this->type = $type;
        $this->title = $title;
    }

    public function toArray() {
        return array_merge(
                array(
            'status' => $this->statusCode,
            'type' => $this->type,
            'title' => $this->title,
                ), $this->extraData
        );
    }

    public function set($name, $value) {
        $this->extraData[$name] = $value;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * @param mixed $statusCode
     */
    public function setStatusCode($statusCode) {
        $this->statusCode = $statusCode;
    }

}