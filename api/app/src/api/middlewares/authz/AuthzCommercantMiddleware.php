<?php

namespace api\middlewares\authz;

use application_core\application\usecases\AuthzUserService;

class AuthzCommercantMiddleware extends AuthzUserMiddleware {
    public function __construct(AuthzUserService $authz) {
        parent::__construct($authz, [AuthzUserService::ROLE_COMMERCANT]);
    }
}