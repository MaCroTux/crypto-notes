<?php

namespace CryptoNotes\Infrastructure\Api\Rest\Middleware;

use CryptoNotes\Infrastructure\Jwt\ValidateJwtService;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class TokenValidateMiddleware
{
    private ValidateJwtService $validateJwtService;

    public function __construct(ValidateJwtService $validateJwtService)
    {
        $this->validateJwtService = $validateJwtService;
    }

    public function __invoke(Request $request, RequestHandlerInterface $handler) {
        $cookieParams = $request->getCookieParams();

        $token = $cookieParams['token'] ?? null;

        $response = new Response();
        $response = $response->withHeader('Content-type', 'application/json');
        $errorMessage = [
            'result' => 'fail',
            'message' => 'Token is required, verify OTP validation',
        ];

        if (null !== $token) {
            try {
                $parse = new Parser(new JoseEncoder());
                $token = $parse->parse($token);
            } catch (CannotDecodeContent $e) {
                $response->getBody()->write(json_encode($errorMessage));
                return $response;
            }

            if ($this->validateJwtService->__invoke($token)) {
                return $handler->handle($request);
            }
        }

        $response->getBody()->write(json_encode($errorMessage));
        return $response;
    }
}