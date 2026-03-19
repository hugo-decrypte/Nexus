<?php
namespace api\middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpUnauthorizedException;

class JwtAuthMiddleware {
    private string $secret;

    public function __construct(string $secret) {
        $this->secret = $secret;
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $next) : ResponseInterface {
        $header = $request->getHeaderLine('Authorization');

        if (empty($header) || !preg_match('/^Bearer\s+(.*)$/i', $header, $matches)) {
            throw new HttpUnauthorizedException($request, 'Token manquant ou mal formé');
        }

        $token = $matches[1];

        try {
            $payload = JWT::decode($token, new Key($this->secret, 'HS512'));

            if (!empty($payload->mfa_pending) && $payload->mfa_pending === true) {
                throw new HttpUnauthorizedException($request, 'Token pré-connexion : validez le code e-mail.');
            }

            $request = $request->withAttribute('user_payload', $payload);
        } catch (HttpUnauthorizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new HttpUnauthorizedException($request, 'Token invalide', $e);
        }

        return $next->handle($request);
    }
}