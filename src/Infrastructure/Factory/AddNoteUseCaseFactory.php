<?php

namespace CryptoNotes\Infrastructure\Factory;

use CryptoNotes\Application\AddNoteUseCase;
use CryptoNotes\CryptService;
use CryptoNotes\Infrastructure\Persistence\File\JsonNotesRepository;

class AddNoteUseCaseFactory
{
    public static function create(
        string $jsonNotesFileName
    ): AddNoteUseCase {
        return new AddNoteUseCase(
            new JsonNotesRepository($jsonNotesFileName),
            new CryptService()
        );
    }
}