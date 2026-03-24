<?php

namespace application_core\exceptions;

use Exception;

class SendMailException extends Exception
{

    public function __construct()
    {
        parent::__construct("Problème lors de l'envoi du mail.", 404);
    }
}