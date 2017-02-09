<?php

namespace Storage;

use Storage\Exception;
use Storage\Model;

class Storage
{
    const FILES = 'files';
    const FILE = 'file';
    const DATA = 'data';

    /**
     * @var array
     */
    protected static $_config = null;

    /**
     * @var string
     */
    protected static $_token = null;

    /**
     * @var \Zend\Http\Client
     */
    protected $_client = null;

    /**
     * @return array
     */
    public static function getConfig()
    {
        return self::$_config;
    }

    /**
     * @param array $config
     */
    public static function setConfig(array $config)
    {
        self::$_config = $config;
    }

    /**
     * @return string
     */
    public static function getToken()
    {
        return self::$_token;
    }

    /**
     * @param string $token
     */
    public static function setToken($token)
    {
        self::$_token = $token;
    }

    /**
     * @throws Exception\ConfigWasNotProvided
     * @throws Exception\TokenWasNotProvided
     */
    public function __construct()
    {
        if (!self::getConfig()) {
            throw new Exception\ConfigWasNotProvided();
        }

        if (!self::getToken()) {
            throw new Exception\TokenWasNotProvided();
        }

        $this->_client = new \Zend\Http\Client();

        $this->_client->setHeaders([
            'x-auth' => self::getToken()
        ]);
    }

    /**
     * @param int $from
     * @param int $count
     *
     * @return Model\File[]
     */
    public function getList($from = 0, $count = 10)
    {
        $this->_client->setUri(implode('/', [
            self::getConfig()['uri'],
            'file/list'
        ]));

        $this->_client->setParameterGet([
            'from' => $from,
            'to' => $count
        ]);

        $filesInfo = json_decode($this->_client->send()->getBody(), true);

        return $this->_convertFilesInfoToObject($filesInfo);
    }

    /**
     * @param string|array $identities
     *
     * @return Model\File[]|mixed
     */
    public function getInfo($identities)
    {
        $this->_client->setUri(implode('/', [
            self::getConfig()['uri'],
            'file/info'
        ]));

        $stringItem = is_string($identities);

        if (!is_array($identities)) {
            $identities = (array)$identities;
        }

        $this->_client->setParameterGet([
            'ids' => $identities
        ]);

        $filesInfo = json_decode($this->_client->send()->getBody(), true);

        $filesInfo = $this->_convertFilesInfoToObject($filesInfo);

        if (!empty($filesInfo) && $stringItem) {
            return $filesInfo[0];
        }

        return $filesInfo;
    }

    /**
     * @param string $query
     * @param int $from
     * @param int $count
     *
     * @return Model\File[]
     */
    public function search($query, $from = 0, $count = 10)
    {
        $this->_client->setUri(implode('/', [
            self::getConfig()['uri'],
            'file/search'
        ]));

        $this->_client->setParameterGet([
            'query' => $query,
            'from' => $from,
            'count' => $count
        ]);

        $filesInfo = json_decode($this->_client->send()->getBody(), true);

        return $this->_convertFilesInfoToObject($filesInfo);
    }

    /**
     * @return int
     */
    public function getCount()
    {
        $this->_client->setUri(implode('/', [
            self::getConfig()['uri'],
            'file/count-total'
        ]));

        echo $this->_client->send()->getBody(); die("\n\n\n");

        $response = json_decode($this->_client->send()->getBody(), true);

        return $response['count'];
    }

    /**
     * @param string|array $identities
     *
     * @return bool
     */
    public function delete($identities)
    {
        $this->_client->setUri(implode('/', [
            self::getConfig()['uri'],
            'file/delete'
        ]));

        if (!is_array($identities)) {
            $identities = (array)$identities;
        }

        $this->_client->setParameterGet([
            'ids' => $identities
        ]);

        return $this->_client->send()->getStatusCode() == 200;
    }

    /**
     * @param mixed $data
     * @param string $type
     *
     * @return Model\File[]
     *
     * @throws Exception\UploadFileTypeWasNotSpecified
     * @throws \Exception
     */
    public function upload($data, $type)
    {
        switch ($type) {

            case self::FILES:
                return $this->_uploadFromFiles($data);

            case self::FILE:
                return $this->_uploadExistingFile($data);

            case self::DATA:
                return $this->_uploadContent($data);

            default:
                throw new Exception\UploadFileTypeWasNotSpecified();
        }
    }

    /**
     * @param string $fileKey
     * @return Model\File[]
     *
     * @throws \Exception
     * @throws \Zend_Http_Client_Exception
     */
    private function _uploadFromFiles($fileKey = null)
    {
        if (!count($_FILES[$fileKey]['name'])) {
            throw new \Exception();
        }

        $files = array_map(function($name, $type, $tmpName, $error, $size){
            return [
                'name' => $name,
                'type' => $type,
                'tmpName' => $tmpName,
                'error' => $error,
                'size' => $size
            ];

        },  $_FILES[$fileKey]['name'],
            $_FILES[$fileKey]['type'],
            $_FILES[$fileKey]['tmp_name'],
            $_FILES[$fileKey]['error'],
            $_FILES[$fileKey]['size']
        );


        foreach ($files as $file) {

            $this->_client->setFileUpload(
                $file['name'],
                'files[]',
                file_get_contents($file['tmpName'])
            );
        }

        return $this->_upload();
    }

    /**
     * @param array $contents
     *
     * @return Model\File[]
     * @throws \Zend_Http_Client_Exception
     */
    private function _uploadContent($contents = null)
    {
        if (is_string(array_keys($contents)[0])) {
            $contents = array($contents);
        }

        foreach ($contents as $content) {
            $this->_client->setFileUpload(
                $content['name'],
                'files[]',
                $content['content']
            );
        }

        return $this->_upload();
    }

    /**
     * @param string|array $filePaths
     *
     * @return Model\File[]
     * @throws \Zend_Http_Client_Exception
     */
    private function _uploadExistingFile($filePaths)
    {
        $files = (array)$filePaths;

        foreach ($files as $file) {
            $this->_client->setFileUpload($file, 'files[]');
        }

        return $this->_upload();
    }

    /**
     * @return Model\File[]
     * @throws \Zend_Http_Client_Exception
     */
    private function _upload()
    {
        $this->_client->setUri(implode('/', [
            self::getConfig()['uri'],
            'file/upload'
        ]));

        $this->_client->setMethod('POST');

        $filesInfo = json_decode($this->_client->send()->getBody(), true);

        return $this->_convertFilesInfoToObject($filesInfo);
    }

    /**
     * @param array $filesInfo
     * @return array
     */
    private function _convertFilesInfoToObject(array $filesInfo)
    {
        $files = [];

        foreach ($filesInfo as $fileInfo) {

            $file = new Model\File();
            $file->populate($fileInfo);

            $files[] = $file;
        }

        return $files;
    }
}