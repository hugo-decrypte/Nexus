<?php

namespace application_core\exceptions;

use Exception;

class InsufficientRightsAuthzException extends Exception
{

    public function __construct(string $message, ?Exception $previous = null)
    {
        parent::__construct($message, 405, $previous);
    }
}