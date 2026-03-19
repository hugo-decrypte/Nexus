<?php

namespace api\actions;

use application_core\application\usecases\interfaces\ServiceAuthnInterface;
use application_core\exceptions\ConnexionException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpInternalServerErrorException;

class VerifyLoginOtpAction
{
    private ServiceAuthnInterface $authnService;

    public function __construct(ServiceAuthnInterface $authnService)
    {
        $this->authnService = $authnService;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();
        if (!is_array($body)) {
            $body = [];
        }
        $pendingToken = trim((string) ($body['pending_token'] ?? ''));
        $code = trim((string) ($body['code'] ?? ''));

        if ($pendingToken === '' || $code === '') {
            $response->getBody()->write(json_encode(['error' => 'pending_token et code requis.']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        $host = $request->getUri()->getHost();

        try {
            $result = $this->authnService->completeLoginWithOtp($pendingToken, $code, $host);
        } catch (ConnexionException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        } catch (\Throwable $e) {
            throw new HttpInternalServerErrorException($request, $e->getMessage());
        }

        $responseData = [
            'id' => $result['id'],
            'nom' => $result['nom'],
            'prenom' => $result['prenom'],
            'role' => $result['role'],
            'email' => $result['email'],
            'token' => $result['token'],
        ];
        $response->getBody()->write(json_encode($responseData));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
