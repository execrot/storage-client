<?php

namespace Storage\Exception;

class ConfigWasNotProvided extends \Exception
{
    public function __construct()
    {
        parent::__construct("Config was not provided");
    }
}