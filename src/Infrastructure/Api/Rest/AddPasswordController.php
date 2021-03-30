<?php

namespace CryptoNotes\Infrastructure\Api\Rest;

use CryptoNotes\Domain\CryptInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;

class AddPasswordController
{
    private CryptInterface $crypt;

    public function __construct(CryptInterface $crypt)
    {
        $this->crypt = $crypt;
    }

    public function __invoke(Request $request, ResponseInterface $response, $args): ResponseInterface
    {
        $secret = $_ENV['SECRET'];
        $response = $response->withHeader('Content-type', 'application/json');
        $password = $args['password'] ?? null;

        if (null === $password) {
            $response->getBody()->write(
                json_encode(
                    [
                        'result' => 'fail',
                        'message' => 'Password is required',
                    ]
                )
            );

            return $response;
        }

        try {
            $passwordCrypt = $this->crypt->encrypt($password, $secret);
            $rest = [
                'password' => $passwordCrypt,
            ];
            setcookie('password', $passwordCrypt, time() + (60 * 60 * 24), "/", $_ENV['HOST'], false, true);
        } catch (\Exception $e) {
            $rest = [
                'result' => 'fail',
                'message' => $e->getMessage(),
            ];
        }

        $response->getBody()->write(json_encode($rest));

        return $response;
    }
}