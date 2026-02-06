<?php

namespace api\middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Psr7\Response;

class CorsMiddleware {
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $next) : Response {
        if ($request->getMethod() === 'OPTIONS') {
            $response = new Response();
            return $this->addCorsHeaders($response, $request);
        }

        $response = $next->handle($request);
        return $this->addCorsHeaders($response, $request);
    }

    private function addCorsHeaders(Response $response, ServerRequestInterface $request): Response {
        $origin = $request->hasHeader('Origin')
            ? $request->getHeaderLine('Origin')
            : '';

        // Liste des origines autorisées
        $allowedOrigins = [
            'http://myapp.net',
            'http://localhost:55120',
            'http://localhost:8080',
        ];

        // En développement, on autorise localhost et 127.0.0.1 (avec ou sans port)
        if (preg_match('/^https?:\/\/localhost(:\d+)?$/', $origin) || preg_match('/^https?:\/\/127\.0\.0\.1(:\d+)?$/', $origin)) {
            $allowedOrigin = $origin;
        } elseif (in_array($origin, $allowedOrigins)) {
            $allowedOrigin = $origin;
        } else {
            $allowedOrigin = 'http://myapp.net'; // Fallback
        }

        return $response
            ->withHeader('Access-Control-Allow-Origin', $allowedOrigin)
            ->withHeader('Access-Control-Allow-Methods', 'POST, PUT, GET, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->withHeader('Access-Control-Max-Age', '3600')
            ->withHeader('Access-Control-Allow-Credentials', 'true');
    }
}