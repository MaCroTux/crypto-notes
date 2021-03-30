<?php

namespace CryptoNotes\Application;

use CryptoNotes\Domain\CryptInterface;
use CryptoNotes\Domain\CryptOperationException;
use CryptoNotes\Domain\Note;
use CryptoNotes\Domain\NotesCollection;
use CryptoNotes\Domain\NotesRepository;

class ListNotesAndDecryptUseCase
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
     * @return NotesCollection
     */
    public function __invoke(string $password): NotesCollection
    {
        $notes = $this->notesRepository->all()->toArray();

        $notes = array_map(
            function (array $rawNote) use ($password) {
                $note = Note::fromArray($rawNote);
                try {
                    $note = $note->withDecryptContents(
                        $this->crypt->decrypt($password, $note->contents())
                    );
                }catch (CryptOperationException $e) {
                    $note = $note->fromNoVisible($rawNote);
                }

                return $note;
            },
            $notes
        );

        return NotesCollection::build(...$notes);
    }
}