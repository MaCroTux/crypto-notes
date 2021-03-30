<?php

namespace CryptoNotes\Infrastructure\Factory;

use CryptoNotes\Application\DeleteNoteUseCase;
use CryptoNotes\Infrastructure\Persistence\File\JsonNotesRepository;

class DeleteNoteUseCaseFactory
{
    public static function create(
        string $jsonNotesFileName
    ): DeleteNoteUseCase {
        return new DeleteNoteUseCase(
            new JsonNotesRepository($jsonNotesFileName)
        );
    }
}