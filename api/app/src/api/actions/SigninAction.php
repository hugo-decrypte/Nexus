<?php

namespace api\actions;

use application_core\application\usecases\interfaces\ServiceAuthnInterface;
use application_core\exceptions\AccountNotValidatedException;
use application_core\exceptions\ConnexionException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpInternalServerErrorException;

class SigninAction {

    private ServiceAuthnInterface $authnService;

    public function __construct(ServiceAuthnInterface $authnService) {
        $this->authnService = $authnService;
    }

    public function __invoke(Request $request, Response $response): Response {
        $utilisateur_dto = $request->getAttribute('auth_dto');

        if ($utilisateur_dto === null) {
            throw new HttpInternalServerErrorException($request, "Erreur de configuration du middleware.");
        }

        $host = $request->getUri()->getHost();

        try {
            $result = $this->authnService->signin($utilisateur_dto, $host);

            if (!empty($result['pending'])) {
                $responseData = [
                    'needsOtp' => true,
                    'pending_token' => $result['pending_token'],
                    'email_masked' => $result['email_masked'],
                ];
                $response->getBody()->write(json_encode($responseData));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            }

            $responseData = [
                'id' => $result['id'],
                'nom' => $result['nom'],
                'prenom' => $result['prenom'],
                'role' => $result['role'],
                'token' => $result['token'],
            ];

            $response->getBody()->write(json_encode($responseData));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } catch (ConnexionException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        } catch (AccountNotValidatedException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(403);
        } catch (\Exception $e) {
            $code = (int) $e->getCode();
            if ($code === 503) {
                $response->getBody()->write(json_encode(['error' => $e->getMessage()]));

                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(503);
            }
            throw $e;
        }
    }
}