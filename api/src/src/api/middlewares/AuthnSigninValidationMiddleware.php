<?php

namespace api\middlewares;

use api\dtos\InputAuthnDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;
use Slim\Exception\HttpBadRequestException;

class AuthnSigninValidationMiddleware {

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $next) : ResponseInterface {

        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $data = [];
        }

        $data["email"] = $data["email"] ?? "";
        $data["mot_de_passe"] = $data["mot_de_passe"] ?? "";


        try {
            v::key('email', v::stringType()->notEmpty()->email())
            ->key('mot_de_passe', v::stringType()->notEmpty())
                ->assert($data);

        } catch (NestedValidationException $e) {
            throw new HttpBadRequestException($request, "Données invalides : " . $e->getFullMessage(), $e);
        }

        $authDto = new InputAuthnDTO($data);

        $request = $request->withAttribute('auth_dto', $authDto);

        return $next->handle($request);
    }
}