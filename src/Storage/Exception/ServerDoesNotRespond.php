<?php

namespace Storage\Exception;

class ServerDoesNotRespond extends \Exception
{
    public function __construct()
    {
        parent::__construct("Server does not respond");
    }
}