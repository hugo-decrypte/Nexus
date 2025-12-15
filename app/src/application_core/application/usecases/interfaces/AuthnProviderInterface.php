<?php

namespace nexus\core\application\usecases\interfaces;

use nexus\api\dtos\AuthnDTO;
use nexus\api\dtos\InputAuthnDTO;

interface AuthnProviderInterface {
    public function signin(InputAuthnDTO $user_dto): AuthnDTO;
}