<?php

namespace CryptoNotes\Infrastructure\Api\Rest;

use CryptoNotes\Application\ListNotesAndDecryptUseCase;
use CryptoNotes\Domain\CryptInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;

class IndexController
{
    private ListNotesAndDecryptUseCase $listNotesAndDecryptUseCase;
    private CryptInterface $crypt;

    public function __construct(
        ListNotesAndDecryptUseCase $listNotesAndDecryptUseCase,
        CryptInterface $crypt
    ) {
        $this->listNotesAndDecryptUseCase = $listNotesAndDecryptUseCase;
        $this->crypt = $crypt;
    }

    public function __invoke(Request $request, ResponseInterface $response, $args): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $response = $response->withHeader('Content-type', 'application/json');
        $passwordCrypt = $queryParams['password'] ?? $args['password'] ?? $_COOKIE['password'] ?? null;

        if (null === $passwordCrypt) {
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
            $password = $this->crypt->decrypt($_ENV['SECRET'], $passwordCrypt);
            $listNotes = $this->listNotesAndDecryptUseCase->__invoke($password);
            $rest = $listNotes->toArray();
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