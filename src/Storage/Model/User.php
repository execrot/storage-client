<?php

namespace Storage\Model;

class User extends AbstractModel
{
    /**
     * @var String
     */
    protected $_id;

    /**
     * @var
     */
    protected $_email;

    /**
     * @var array
     */
    protected $_tokens = [];

    /**
     * @return String
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param String $id
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->_email = $email;
    }

    /**
     * @return array
     */
    public function getTokens()
    {
        return $this->_tokens;
    }

    /**
     * @param array $tokens
     */
    public function setTokens($tokens)
    {
        $this->_tokens = $tokens;
    }
}