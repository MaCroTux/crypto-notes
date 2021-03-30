<?php

namespace CryptoNotes\Infrastructure\Api\Rest;

use CryptoNotes\Application\AddNoteUseCase;
use CryptoNotes\Domain\CryptInterface;
use CryptoNotes\Domain\CryptOperationException;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;

class AddNoteController
{
    private AddNoteUseCase $addNoteUseCase;
    private CryptInterface $crypt;

    public function __construct(
        AddNoteUseCase $addNoteUseCase,
        CryptInterface $crypt
    ) {
        $this->addNoteUseCase = $addNoteUseCase;
        $this->crypt = $crypt;
    }

    /**
     * @param Request $request
     * @param ResponseInterface $response
     * @param $args
     * @return ResponseInterface
     */
    public function __invoke(Request $request, ResponseInterface $response, $args): ResponseInterface
    {
        $response = $response->withHeader('Content-type', 'application/json');
        $postArgs = $request->getParsedBody();

        if (empty($postArgs) && !empty($request->getBody()->getContents())) {
            $postArgs = json_decode($request->getBody()->getContents(), true);
        }

        if (null === $postArgs) {
            $rest = [
                'result' => 'fail',
                'message' => 'No data',
            ];
            $response->getBody()->write(json_encode($rest));

            return $response;
        }

        $secret = $_ENV['SECRET'];
        $passwordCrypt = $postArgs['password'] ?? $_COOKIE['password'] ?? null;

        try {
            $password = $this->crypt->decrypt($secret, $passwordCrypt);

            $note = $this->addNoteUseCase->__invoke(
                $password,
                $postArgs['name'] ?? '',
                $postArgs['contents'] ?? ''
            );

            $rest = [
                $note->toArray()
            ];
        } catch (CryptOperationException|Exception $e) {
            $rest = [
                'result' => 'fail',
                'message' => $e->getMessage(),
            ];
        }

        $response->getBody()->write(json_encode($rest));

        return $response;
    }
}