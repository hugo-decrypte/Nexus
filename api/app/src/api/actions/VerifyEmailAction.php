<?php

namespace api\actions;

use application_core\application\usecases\interfaces\ServiceAuthnInterface;
use application_core\exceptions\EntityNotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class VerifyEmailAction {

    private ServiceAuthnInterface $serviceAuthn;

    public function __construct(ServiceAuthnInterface $serviceAuthn) {
        $this->serviceAuthn = $serviceAuthn;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $token = $queryParams['token'] ?? null;

        if (!$token) {
            $response->getBody()->write(json_encode(["error" => "Token manquant"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $this->serviceAuthn->validateAccount($token);

            $html = <<<HTML
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Compte Validé</title>
                    <style>
                        body { font-family: sans-serif; text-align: center; padding: 50px; background: #f4f4f4; }
                        .card { background: white; padding: 40px; border-radius: 8px; display: inline-block; shadow: 0 2px 10px rgba(0,0,0,0.1); }
                        h1 { color: #27ae60; }
                        a { color: #3498db; text-decoration: none; font-weight: bold; }
                    </style>
                </head>
                <body>
                    <div class="card">
                        <h1>✅ Validation réussie !</h1>
                        <p>Votre compte Nexus est désormais actif.</p>
                        <p>Vous pouvez maintenant fermer cette page.</p>
                    </div>
                </body>
                </html>
        HTML;

            $response->getBody()->write($html);
            return $response->withStatus(200);

        } catch (EntityNotFoundException $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(404);

        } catch (\Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus($e->getCode());
        }
    }
}