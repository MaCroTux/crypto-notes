<?php

namespace CryptoNotes\Infrastructure\Factory;

use CryptoNotes\Application\ListNotesAndDecryptUseCase;
use CryptoNotes\CryptService;
use CryptoNotes\Infrastructure\Persistence\File\JsonNotesRepository;

class ListNotesAndDecryptUseCaseFactory
{
    public static function create(
        string $jsonNotesFileName
    ): ListNotesAndDecryptUseCase {
        return new ListNotesAndDecryptUseCase(
            new JsonNotesRepository($jsonNotesFileName),
            new CryptService()
        );
    }
}