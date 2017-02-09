<?php

namespace Storage;

use Storage\Exception;

class Account
{
    /**
     * @var \Zend\Http\Client
     */
    private $_client = null;

    /**
     * @var string
     */
    private $_lastError = null;

    /**
     * @return string
     */
    public function getLastError()
    {
        return $this->_lastError;
    }

    /**
     * Account constructor.
     * @throws Exception\ConfigWasNotProvided
     */
    public function __construct()
    {
        if (!Storage::getConfig()) {
            throw new Exception\ConfigWasNotProvided();
        }

        $this->_client = new \Zend\Http\Client();
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return bool
     */
    public function register($email, $password)
    {
        $this->_client->setParameterGet([
            'email'    => $email,
            'password' => $password
        ]);

        $this->_client->setUri(implode('/', [Storage::getConfig()['uri'], 'user', 'register']));

        $this->_client->setMethod('post');
        $response = $this->_client->send();

        if ($response->getStatusCode() !== 200) {

            try {
                $body = json_decode($response->getBody(), true);
                $this->_lastError = $body['message'];
            }
            catch (Exception\ServerDoesNotRespond $e) {
                $this->_lastError = $e->getMessage();
            }

            return false;
        }

        return true;
    }

    public function auth($email, $password)
    {
        $this->_client->setParameterGet([
            'email' => $email,
            'password' => $password
        ]);

        $this->_client->setUri(implode('/', [Storage::getConfig()['uri'], 'user', 'auth']));

        $response = $this->_client->send();

        if ($response->getStatusCode() != 200) {

            try {
                $body = json_decode($response->getBody(), true);
                $this->_lastError = $body['message'];
            }
            catch (Exception\ServerDoesNotRespond $e) {
                $this->_lastError = $e->getMessage();
            }

            return false;
        }

        $body = json_decode($response->getBody(), true);

        $user = new Model\User();
        $user->populate($body['user']);

        return $user;
    }
}