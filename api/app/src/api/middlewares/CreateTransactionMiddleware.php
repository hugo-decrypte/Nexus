<?php

namespace api\middlewares;

use api\dtos\InputTransactionDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;
use Slim\Exception\HttpBadRequestException;

class CreateTransactionMiddleware {
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $next) : ResponseInterface {

        $data = $request->getParsedBody();
        if (!is_array($data)) {
            throw new HttpBadRequestException($request, 'Body JSON invalide.');
        }

        $montant = isset($data['montant']) ? (float) $data['montant'] : null;
        if ($montant === null || $montant <= 0) {
            throw new HttpBadRequestException($request, 'montant (positif) requis.');
        }
        $data['montant'] = $montant;
        $data['id_emetteur'] = isset($data['id_emetteur']) ? (string) $data['id_emetteur'] : '';
        $data['id_recepteur'] = isset($data['id_recepteur']) ? (string) $data['id_recepteur'] : '';

        try {
            v::key('id_emetteur', v::stringType()->notEmpty())
                ->key('id_recepteur', v::stringType()->notEmpty())
                ->key('montant', v::floatType()->notEmpty())
                ->key('description', v::optional(v::stringType()))
                ->assert($data);

        } catch (NestedValidationException $e) {
            throw new HttpBadRequestException($request, "Invalid data: " . $e->getFullMessage(), $e);
        }

        $transDTO = new InputTransactionDTO($data);
        $request = $request->withAttribute('transaction_dto', $transDTO);

        return $next->handle($request);
    }
}