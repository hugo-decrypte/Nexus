<?php

namespace application_core\exceptions;

use Exception;

class SendMailException extends Exception
{

    public function __construct()
    {
        //401 unauthorized
        parent::__construct("Poblème lors de l'envoi du mail.", 404);
    }
}