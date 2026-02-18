<?php

namespace application_core\exceptions;

use Exception;

class AccountNotValidatedException extends Exception
{

    public function __construct()
    {
        parent::__construct("Votre compte n'est pas encore validé. Veuillez consulter vos emails.", 403);
    }
}