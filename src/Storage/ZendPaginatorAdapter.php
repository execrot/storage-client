<?php

namespace Storage;

class ZendPaginatorAdapter implements \Zend_Paginator_Adapter_Interface
{
    /**
     * @var Storage
     */
    private $_storage = null;

    /**
     * @param Storage $storage
     */
    public function setStorage(Storage $storage)
    {
        $this->_storage = $storage;
    }

    /**
     * Returns an collection of items for a page.
     *
     * @param  integer $offset Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        return $this->_storage->getList($offset, $itemCountPerPage);
    }

    /**
     * Count elements of an object
     *
     * @return int
     */
    public function count()
    {
        return $this->_storage->getCount();
    }
}