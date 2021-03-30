<?php

namespace CryptoNotes\Application;

use CryptoNotes\Domain\NotesRepository;

class DeleteNoteUseCase
{
    private NotesRepository $notesRepository;

    public function __construct(NotesRepository $notesRepository) {
        $this->notesRepository = $notesRepository;
    }

    public function __invoke(string $noteId): void
    {
        $this->notesRepository->delete($noteId);
    }
}