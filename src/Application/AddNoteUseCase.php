<?php

namespace CryptoNotes\Application;

use CryptoNotes\Domain\CryptInterface;
use CryptoNotes\Domain\CryptOperationException;
use CryptoNotes\Domain\Note;
use CryptoNotes\Domain\NotesRepository;

class AddNoteUseCase
{
    private NotesRepository $notesRepository;
    private CryptInterface $crypt;

    public function __construct(
        NotesRepository $notesRepository,
        CryptInterface $crypt
    ) {
        $this->notesRepository = $notesRepository;
        $this->crypt = $crypt;
    }

    /**
     * @param string $password
     * @param string $name
     * @param string $contents
     * @return Note
     * @throws CryptOperationException
     */
    public function __invoke(string $password, string $name, string $contents): Note
    {
        $note = Note::build(
            uniqid(),
            $name,
            $this->crypt->encrypt($contents, $password)
        );

        $this->notesRepository->save($note);

        return $note;
    }
}