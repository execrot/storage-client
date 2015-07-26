<?php

namespace Storage\Exception;

class TokenWasNotProvided extends \Exception
{
    public function __construct()
    {
        parent::__construct("Token was not provided");
    }
}