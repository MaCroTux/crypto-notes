<?php

namespace CryptoNotes\Infrastructure\Api\Rest;

use CryptoNotes\Application\DeleteNoteUseCase;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;

class DeleteNoteController
{
    private DeleteNoteUseCase $deleteNoteUseCase;

    public function __construct(DeleteNoteUseCase $deleteNoteUseCase)
    {
        $this->deleteNoteUseCase = $deleteNoteUseCase;
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

        $rest = [
            'result' => 'ok'
        ];

        try {
            $this->deleteNoteUseCase->__invoke(
                $args['id'] ?? ''
            );
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