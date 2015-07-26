<?php

namespace Storage\Model;

class File extends AbstractModel
{
    /**
     * @var string
     */
    protected $_identity = null;

    /**
     * @var string
     */
    protected $_name = null;

    /**
     * @var string
     */
    protected $_type = null;

    /**
     * @var int
     */
    protected $_size = null;

    /**
     * @var string
     */
    protected $_url = null;

    /**
     * @return string
     */
    public function getIdentity()
    {
        return $this->_identity;
    }

    /**
     * @param string $identity
     */
    public function setIdentity($identity)
    {
        $this->_identity = $identity;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->_size = $size;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->_url = $url;
    }
}