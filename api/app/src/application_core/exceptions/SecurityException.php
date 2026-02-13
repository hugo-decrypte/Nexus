<?php

namespace application_core\exceptions;

use Exception;

class SecurityException extends Exception
{

    public function __construct()
    {
        //401 unauthorized
        parent::__construct("Blocage administrateur.", 401);
    }
}