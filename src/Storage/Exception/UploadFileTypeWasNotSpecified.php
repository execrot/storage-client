<?php

namespace Storage\Exception;

class UploadFileTypeWasNotSpecified extends \Exception
{
    public function __construct()
    {
        parent::__construct("Upload file type was not specified");
    }
}