<?php

namespace application_core\application\usecases\interfaces;


use api\dtos\AuthnDTO;
use api\dtos\InputAuthnDTO;

interface AuthnProviderInterface {
    public function signin(InputAuthnDTO $user_dto): AuthnDTO;
}